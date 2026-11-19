<footer class="border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/60 py-8 mt-auto transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 text-center sm:text-left">
            <div class="flex items-center gap-3">
                <div class="h-7 w-7 rounded-lg bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center p-1.5 shadow-sm">
                    <img src="{{ asset('documents/Nexo_Logo.png') }}" alt="Logo NEXO" class="h-full w-full object-contain brightness-0 invert">
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">NEXO · Portal de Estágio UFCI-RJ</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">© {{ date('Y') }} Todos os direitos reservados.</p>
                </div>
            </div>
            <div class="flex items-center gap-6 text-sm text-slate-600 dark:text-slate-400">
                <a href="/tutorial" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Tutorial</a>
                <a href="/empresas" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Empresas</a>
                <span class="text-xs text-slate-500 dark:text-slate-400">UFCI-RJ</span>
            </div>
        </div>
    </div>
</footer>
