<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    private const TIPOS_CADASTRO = ['aluno', 'supervisor', 'orientador'];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tipo = strtolower((string) $this->input('tipo'));

        return [
            'nome'            => ['required', 'string', 'max:60'],
            'email'           => ['required', 'email', 'max:45', 'unique:usuarios,Email'],
            'cpf'             => ['required', 'string', 'cpf_valido'],
            'senha'           => ['required', 'string', 'min:6', 'same:confirmar_senha'],
            'confirmar_senha' => ['required', 'string'],
            'tipo'            => ['required', Rule::in(self::TIPOS_CADASTRO)],
            'matricula'       => [$tipo === 'aluno' ? 'required' : 'nullable', 'string', 'max:30', Rule::unique('alunos', 'matricula')],
            'curso'           => [$tipo === 'aluno' ? 'required' : 'nullable', 'in:Informatica,Automacao Industrial,Telecomunicações,Enfermagem'],
            'siape'           => [$tipo === 'orientador' ? 'required' : 'nullable', 'digits:8', Rule::unique('orientadores', 'siape')],
        ];
    }

    public function messages(): array
    {
        return [
            'senha.same'         => 'A confirmação de senha não confere.',
            'senha.required'     => 'Informe uma senha.',
            'senha.min'          => 'A senha deve ter pelo menos 6 caracteres.',
            'confirmar_senha.required' => 'Confirme sua senha.',
            'cpf.cpf_valido'     => 'Informe um CPF válido.',
            'email.required'     => 'Informe seu e-mail.',
            'email.email'        => 'Informe um endereço de e-mail válido.',
            'email.unique'       => 'Este e-mail já está cadastrado. Use outro endereço ou faça login.',
            'nome.required'      => 'Informe seu nome completo.',
            'nome.max'           => 'O nome deve ter no máximo 60 caracteres.',
            'matricula.required' => 'Informe sua matrícula de aluno.',
            'matricula.max'      => 'A matrícula deve ter no máximo 30 caracteres.',
            'matricula.unique'   => 'Esta matrícula já está cadastrada.',
            'curso.required'     => 'Selecione seu curso.',
            'curso.in'           => 'Selecione um dos cursos disponíveis.',
            'siape.required'     => 'Informe seu SIAPE.',
            'siape.digits'       => 'O SIAPE deve conter exatamente 8 dígitos.',
            'siape.unique'       => 'Este SIAPE já está cadastrado.',
            'tipo.in'            => 'Selecione um tipo de usuário válido.',
        ];
    }
}
