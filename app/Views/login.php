<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Plataforma de Profissionais</title>
    <!-- Prevenção do erro 404 do Favicon -->
    <link rel="icon" href="data:,">
    <!-- Tailwind CSS (via CDN para facilitar na tela de login) -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 h-screen flex items-center justify-center">

    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-sm border border-slate-200">
        
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-slate-800 uppercase tracking-wider">Acesso Restrito</h2>
            <p class="text-sm text-slate-500 mt-1">Insira suas credenciais para continuar</p>
        </div>
        
        <!-- Exibe o erro se o usuário errar a senha -->
        <?php if (!empty($erro)): ?>
            <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-4 mb-6 rounded-r">
                <p class="text-sm font-medium"><?= $erro; ?></p>
            </div>
        <?php endif; ?>

        <!-- Formulário enviando para o método autenticar -->
        <form action="/login/autenticar" method="POST">
            <div class="mb-5">
                <label class="block text-slate-700 text-sm font-bold mb-2">Usuário</label>
                <input type="text" name="usuario" class="w-full border border-slate-300 rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required autocomplete="off">
            </div>
            
            <div class="mb-8">
                <label class="block text-slate-700 text-sm font-bold mb-2">Senha</label>
                <input type="password" name="senha" class="w-full border border-slate-300 rounded-lg py-2 px-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
            </div>
            
            <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all">
                Entrar no Sistema
            </button>
        </form>

    </div>

</body>
</html>