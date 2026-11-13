#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
Script de extração de empresas conveniadas a partir de PDF oficial.
Possui suporte a PyMuPDF / pypdf e fallback nativo em Python puro (zlib + regex),
não necessitando de nenhuma dependência externa (pip) para funcionar.
Gera um arquivo .csv intermediário estruturado para posterior carga no banco de dados.
"""

import sys
import os
import re
import csv
import json
import zlib
import calendar
import argparse
from pathlib import Path

CNPJ_PATTERN = re.compile(r'(\d{2}\.\d{3}\.\d{3}/\d{4}-\d{2})')
# Reconhece datas com dias/meses/anos variantes e caracteres de erro (ex: '24/077/2026', '26/10/20222', '09/06./2030', '16Q04/2025')
DATE_PATTERN = re.compile(r'(\d{1,3})[/\-Qq.]+(\d{1,3})[/\-Qq.]+(\d{2,5})')

def clean_date(date_str):
    if not date_str:
        return None
    nums = re.findall(r'\d+', date_str)
    if len(nums) < 3:
        return None
    d, m, y = nums[0], nums[1], nums[2]

    # Correções de Ano (ex: 20222 -> 2022, 20231 -> 2031, 230 -> 2030)
    if len(y) == 5:
        if y == '20222':
            y = '2022'
        elif y == '20231':
            y = '2031'
        elif y.startswith('20'):
            y = y[:4]
    elif len(y) == 3:
        y = '20' + y[1:] if y.startswith('2') else '20' + y
    elif len(y) == 2:
        y = '20' + y

    # Correções de Mês (ex: 077 -> 07, 012 -> 12)
    if len(m) == 3:
        if m.startswith('0'):
            if int(m[1:]) <= 12:
                m = m[1:]
            elif len(m) >= 3 and m[1] == m[2]:
                m = '0' + m[1]

    # Correções de Dia (ex: 047 -> 07, 031 -> 31)
    if len(d) == 3:
        if d.startswith('0'):
            if int(d[1:]) <= 31:
                d = d[1:]
            elif int(d[-2:]) <= 31:
                d = d[-2:]
            else:
                d = d[-1]

    try:
        year_int = int(y)
        month_int = int(m)
        day_int = int(d)
        if 1 <= month_int <= 12 and 1990 <= year_int <= 2040 and day_int >= 1:
            _, max_day = calendar.monthrange(year_int, month_int)
            day_int = max(1, min(day_int, max_day))
            return f"{year_int:04d}-{month_int:02d}-{day_int:02d}"
    except Exception:
        pass
    return None

def parse_lines_to_records(lines):
    records = []
    i = 0
    while i < len(lines):
        line = lines[i]
        cm = CNPJ_PATTERN.search(line)
        if cm:
            cnpj = cm.group(1)

            # Busca nome da empresa nas linhas imediatamente anteriores
            empresa_parts = []
            prev_idx = i - 1
            while prev_idx >= 0:
                prev_line = lines[prev_idx]
                if CNPJ_PATTERN.search(prev_line) or DATE_PATTERN.search(prev_line):
                    break
                if prev_line.upper() in ('VIGÊNCIA', 'VIGENCIA', 'INICIO', 'INÍCIO', 'FINAL', 'EMPRESA', 'CNPJ'):
                    break
                empresa_parts.insert(0, prev_line)
                prev_idx -= 1

            empresa = " ".join(empresa_parts).strip()
            if not empresa:
                empresa = "Empresa Sem Razão Social"

            # Busca datas de vigência nas linhas imediatamente posteriores
            dt_inicio = None
            dt_fim = None
            next_idx = i + 1
            while next_idx < len(lines) and next_idx <= i + 4:
                nxt = lines[next_idx]
                if CNPJ_PATTERN.search(nxt):
                    break
                dm = DATE_PATTERN.search(nxt)
                if dm:
                    cd = clean_date(dm.group(0))
                    if cd:
                        if not dt_inicio:
                            dt_inicio = cd
                        elif not dt_fim:
                            dt_fim = cd
                            next_idx += 1
                            break
                next_idx += 1

            # Inferência de datas caso uma delas tenha ficado vazia (padrão CEFET: vigência de 5 anos)
            if dt_inicio and not dt_fim:
                try:
                    y, m, d = [int(x) for x in dt_inicio.split('-')]
                    dt_fim = clean_date(f"{max(1, d-1):02d}/{m:02d}/{y+5:04d}")
                except Exception:
                    pass
            elif dt_fim and not dt_inicio:
                try:
                    y, m, d = [int(x) for x in dt_fim.split('-')]
                    dt_inicio = clean_date(f"{min(28, d+1):02d}/{m:02d}/{y-5:04d}")
                except Exception:
                    pass

            records.append({
                "empresa": empresa,
                "cnpj": cnpj,
                "vig_inicial": dt_inicio or "",
                "vig_final": dt_fim or ""
            })
            i = next_idx
        else:
            i += 1

    return records

def extract_with_pymupdf(pdf_path):
    import pymupdf as fitz
    doc = fitz.open(str(pdf_path))
    all_lines = []
    for page_idx in range(len(doc)):
        text = doc[page_idx].get_text("text")
        all_lines.extend([l.strip() for l in text.splitlines() if l.strip()])
    doc.close()
    return parse_lines_to_records(all_lines)

def extract_with_pure_python(pdf_path):
    with open(pdf_path, 'rb') as f:
        data = f.read()

    tj_pattern = re.compile(r'\[(.*?)\]\s*TJ', re.DOTALL)
    str_pattern = re.compile(r'\((.*?)\)')
    streams = re.findall(b'stream[\r\n]+(.*?)[\r\n]+endstream', data, re.DOTALL)

    lines = []
    for s in streams:
        try:
            dec = zlib.decompress(s).decode('latin1', 'ignore')
            for match in tj_pattern.finditer(dec):
                parts = str_pattern.findall(match.group(1))
                line = ''.join(parts).strip()
                line = re.sub(r'\\([0-7]{1,3})', lambda m: chr(int(m.group(1), 8)), line)
                line = line.replace('\\(', '(').replace('\\)', ')').replace('\\\\', '\\')
                if line:
                    lines.append(line)
        except Exception:
            pass

    return parse_lines_to_records(lines)

def extract_companies_from_pdf(pdf_path):
    pdf_file = Path(pdf_path)
    if not pdf_file.exists():
        raise FileNotFoundError(f"Arquivo PDF não encontrado: {pdf_path}")

    # 1. Tenta usar PyMuPDF se disponível
    try:
        return extract_with_pymupdf(pdf_file)
    except ImportError:
        pass
    except Exception as e:
        sys.stderr.write(f"Aviso PyMuPDF: {e}. Alternando para extrator nativo...\n")

    # 2. Fallback automático: Extrator nativo (zero dependências pip)
    return extract_with_pure_python(pdf_file)

def main():
    parser = argparse.ArgumentParser(description="Extração de empresas conveniadas de PDF para CSV")
    parser.add_argument("--pdf", required=True, help="Caminho do arquivo PDF")
    parser.add_argument("--csv", required=True, help="Caminho do arquivo CSV de saída")
    parser.add_argument("--json", required=False, help="Caminho opcional do arquivo JSON de saída")
    args = parser.parse_args()

    try:
        records = extract_companies_from_pdf(args.pdf)
        
        if not records:
            raise ValueError("Nenhum registro de empresa foi encontrado no arquivo PDF fornecido.")

        # Garante diretório de destino
        csv_path = Path(args.csv)
        csv_path.parent.mkdir(parents=True, exist_ok=True)

        # Salva o arquivo .csv com cabeçalhos padrão
        with open(csv_path, mode="w", newline="", encoding="utf-8-sig") as f:
            writer = csv.DictWriter(f, fieldnames=["empresa", "cnpj", "vig_inicial", "vig_final"], extrasaction="ignore")
            writer.writeheader()
            for r in records:
                writer.writerow(r)

        if args.json:
            json_path = Path(args.json)
            json_path.parent.mkdir(parents=True, exist_ok=True)
            with open(json_path, mode="w", encoding="utf-8") as f:
                json.dump(records, f, ensure_ascii=False, indent=2)

        output = {
            "status": "success",
            "total": len(records),
            "csv_path": str(csv_path.resolve()),
            "message": f"{len(records)} empresas extraídas com sucesso."
        }
        print(json.dumps(output))
        sys.exit(0)

    except Exception as e:
        output = {
            "status": "error",
            "message": str(e)
        }
        print(json.dumps(output))
        sys.exit(1)

if __name__ == "__main__":
    main()
