<?php require_once __DIR__ . '/templates/header.php'; ?>

<div class="max-w-5xl mx-auto bg-white p-6 rounded-lg shadow mt-10">
    <h1 class="text-2xl font-bold mb-2">Olá Admin</h1>
    <p class="mb-6 text-gray-600">
        Profissionais disponíveis ao total: <strong><?= $total_geral ?? 0; ?></strong> 
        (Exibindo pelo filtro: <strong><?= $total_filtrado ?? 0; ?></strong>)
    </p>

    <!-- Formulário de Filtros Adaptativos -->
    <form method="GET" action="/" class="flex flex-col md:flex-row gap-4 mb-8">
        
        <div class="flex-1">
            <label class="block mb-2 font-medium text-sm text-gray-700">Categoria Profissional:</label>
            <select name="area_atuacao" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500">
                <option value="">Todas as categorias...</option>
                <?php if (!empty($lista_areas)): ?>
                    <?php foreach ($lista_areas as $area): ?>
                        <option value="<?= htmlspecialchars($area) ?>" 
                            <?= (isset($_GET['area_atuacao']) && $_GET['area_atuacao'] == $area) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($area) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        
        <div class="flex-1">
            <label class="block mb-2 font-medium text-sm text-gray-700">Cidade/Região:</label>
            <select name="localizacao" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500">
                <option value="">Todas as cidades...</option>
                <?php if (!empty($lista_localizacoes)): ?>
                    <?php foreach ($lista_localizacoes as $loc): ?>
                        <option value="<?= htmlspecialchars($loc) ?>" 
                            <?= (isset($_GET['localizacao']) && $_GET['localizacao'] == $loc) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($loc) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        
        <div class="w-full md:w-32">
            <label class="block mb-2 font-medium text-sm text-gray-700">Por página:</label>
            <select name="limite" class="w-full border border-gray-300 p-2 rounded focus:ring-2 focus:ring-blue-500">
                <option value="10" <?= (isset($_GET['limite']) && $_GET['limite'] == 10) ? 'selected' : '' ?>>10</option>
                <option value="25" <?= (isset($_GET['limite']) && $_GET['limite'] == 25) ? 'selected' : '' ?>>25</option>
                <option value="100" <?= (isset($_GET['limite']) && $_GET['limite'] == 100) ? 'selected' : '' ?>>100</option>
            </select>
        </div>

        <div class="flex items-end">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 w-full md:w-auto">Filtrar</button>
        </div>
    </form>

    <!-- Tabela de Resultados -->
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="py-2 px-4 border-b text-left text-sm font-semibold text-gray-600">Nome</th>
                    <th class="py-2 px-4 border-b text-left text-sm font-semibold text-gray-600">Categoria</th>
                    <th class="py-2 px-4 border-b text-left text-sm font-semibold text-gray-600">Cidade</th>
                    <th class="py-2 px-4 border-b text-left text-sm font-semibold text-gray-600">Email</th>
                    <th class="py-2 px-4 border-b text-left text-sm font-semibold text-gray-600">WhatsApp</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($candidatos)): ?>
                    <?php foreach ($candidatos as $c): ?>
                        <?php 
                            $zapLimpo = preg_replace('/[^0-9]/', '', $c['whatsapp']);
                            if (strlen($zapLimpo) <= 11 && !empty($zapLimpo)) {
                                $zapLimpo = '55' . $zapLimpo;
                            }
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="py-2 px-4 border-b text-sm"><?= htmlspecialchars($c['nome']) ?></td>
                            <td class="py-2 px-4 border-b text-sm"><?= htmlspecialchars($c['area_atuacao']) ?></td>
                            <td class="py-2 px-4 border-b text-sm"><?= htmlspecialchars($c['localizacao']) ?></td>
                            <td class="py-2 px-4 border-b text-sm">
                                <a href="mailto:<?= htmlspecialchars($c['email']) ?>" class="text-blue-600 hover:underline">
                                    <?= htmlspecialchars($c['email']) ?>
                                </a>
                            </td>
                            <td class="py-2 px-4 border-b text-sm">
                                <a href="https://wa.me/<?= $zapLimpo ?>" target="_blank" class="text-green-600 font-medium hover:underline">
                                    <?= htmlspecialchars($c['whatsapp']) ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="py-6 text-center text-gray-500">Nenhum profissional encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <?php if (isset($paginacao) && $paginacao['total_paginas'] > 1): ?>
        <div class="mt-6 flex justify-center gap-2">
            <?php for ($i = 1; $i <= $paginacao['total_paginas']; $i++): ?>
                <a href="?area_atuacao=<?= urlencode($paginacao['area_atuacao']) ?>&localizacao=<?= urlencode($paginacao['localizacao']) ?>&limite=<?= $paginacao['limite'] ?>&pagina=<?= $i ?>" 
                   class="px-4 py-2 border rounded transition-colors <?= ($paginacao['pagina_atual'] == $i) ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-blue-600 border-gray-300 hover:bg-blue-50' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/templates/footer.php'; ?>