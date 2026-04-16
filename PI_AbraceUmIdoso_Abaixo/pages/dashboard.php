<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/layout_top.php';

require_login();

$user = current_user();
$db = db();
$cards = [];

if ($user['tipo'] === 'V') {
    $cards = [
      ['title' => 'Escrever carta', 'page' => 'escrever_carta.php', 'desc' => 'Envie uma mensagem com carinho para um idoso.'],
      ['title' => 'Histórico de cartas', 'page' => 'historico_cartas.php', 'desc' => 'Consulte cartas enviadas e respostas recebidas.'],
      ['title' => 'Agendar visita', 'page' => 'agendar_visita.php', 'desc' => 'Escolha uma instituição, um idoso e um horário.'],
      ['title' => 'Meu perfil', 'page' => 'editar_perfil.php', 'desc' => 'Atualize seus dados de cadastro.'],
    ];
} elseif ($user['tipo'] === 'I') {
    $cards = [
      ['title' => 'Recebimento de cartas', 'page' => 'recebimento_cartas.php', 'desc' => 'Veja as cartas recebidas e registre a resposta do idoso.'],
      ['title' => 'Cadastrar idoso', 'page' => 'cadastro_idoso.php', 'desc' => 'Cadastre novos idosos vinculados à instituição.'],
      ['title' => 'Cadastrar funcionario', 'page' => 'cadastro_funcionario.php', 'desc' => 'Cadastre novos funcionários da instituição.'],
      ['title' => 'Agenda da instituição', 'page' => 'agenda.php', 'desc' => 'Acompanhe visitas agendadas por data.'],
      ['title' => 'Editar instituição', 'page' => 'editar_perfil.php', 'desc' => 'Mantenha os dados da instituição atualizados.'],
    ];
} else {
    $cards = [
      ['title' => 'Agenda de visitas', 'page' => 'agenda.php', 'desc' => 'Visualize e acompanhe visitas por data.'],
      ['title' => 'Cadastrar idoso', 'page' => 'cadastro_idoso.php', 'desc' => 'Registre idosos vinculados à instituição.'],
      ['title' => 'Editar dados', 'page' => 'editar_perfil.php', 'desc' => 'Atualize seus dados de funcionário.'],
      ['title' => 'Informações', 'page' => 'informacoes.php', 'desc' => 'Consulte registros e indicadores do sistema.'],
    ];
}

function dashboard_message(array $user): array
{
    switch ($user['tipo']) {
        case 'V':
            return [
                'titulo' => 'Bem-vindo(a), ' . $user['nome'] . '!',
                'texto' => 'Obrigado por dedicar seu tempo e carinho. Aqui você pode escrever cartas, acompanhar seu histórico e organizar novas visitas.',
            ];
        case 'I':
            return [
                'titulo' => 'Olá, ' . $user['nome'] . '!',
                'texto' => 'Que bom ter sua instituição conosco. Use este painel para acompanhar cartas, organizar visitas e manter os cadastros atualizados.',
            ];
        default:
            return [
                'titulo' => 'Olá, ' . $user['nome'] . '!',
                'texto' => 'Seu painel está pronto para acompanhar a rotina da instituição, atualizar dados e consultar as informações mais recentes do sistema.',
            ];
    }
}

function table_exists(mysqli $db, string $table): bool
{
    $table = $db->real_escape_string($table);
    $sql = "SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = '{$table}' LIMIT 1";
    $result = $db->query($sql);
    return $result && $result->num_rows > 0;
}

function find_existing_column(mysqli $db, string $table, array $columns): ?string
{
    $table = $db->real_escape_string($table);
    $safeColumns = array_map([$db, 'real_escape_string'], $columns);
    $in = "'" . implode("','", $safeColumns) . "'";

    $sql = "
        SELECT column_name
        FROM information_schema.columns
        WHERE table_schema = DATABASE()
          AND table_name = '{$table}'
          AND column_name IN ({$in})
        LIMIT 1
    ";

    $result = $db->query($sql);
    if ($result && ($row = $result->fetch_assoc())) {
        return $row['column_name'];
    }

    return null;
}

function fetch_latest_activities(mysqli $db, array $user): array
{
    $activities = [];

    $sources = [
        [
            'table' => 'agendamento',
            'label' => 'Visita agendada',
            'date_columns' => ['data_agendamento', 'data_visita', 'created_at', 'data_criacao', 'cadastrado_em'],
            'text' => 'Novo agendamento registrado no sistema.',
        ],
        [
            'table' => 'carta',
            'label' => 'Carta registrada',
            'date_columns' => ['created_at', 'data_criacao', 'data_envio', 'cadastrado_em'],
            'text' => 'Uma nova carta foi cadastrada.',
        ],
        [
            'table' => 'idoso',
            'label' => 'Idoso cadastrado',
            'date_columns' => ['created_at', 'data_criacao', 'cadastrado_em', 'dt_cadastro'],
            'text' => 'Um novo idoso foi incluído no sistema.',
        ],
    ];

    foreach ($sources as $source) {
        if (!table_exists($db, $source['table'])) {
            continue;
        }

        $dateColumn = find_existing_column($db, $source['table'], $source['date_columns']);
        if (!$dateColumn) {
            continue;
        }

        $table = $source['table'];
        $sql = "SELECT {$dateColumn} AS data_ref FROM {$table} WHERE {$dateColumn} IS NOT NULL ORDER BY {$dateColumn} DESC LIMIT 3";
        $result = $db->query($sql);

        if (!$result) {
            continue;
        }

        while ($row = $result->fetch_assoc()) {
            $timestamp = strtotime((string) $row['data_ref']);
            if (!$timestamp) {
                continue;
            }

            $activities[] = [
                'label' => $source['label'],
                'text' => $source['text'],
                'date' => date('d/m/Y H:i', $timestamp),
                'timestamp' => $timestamp,
            ];
        }
    }

    usort($activities, static function (array $a, array $b): int {
        return $b['timestamp'] <=> $a['timestamp'];
    });

    return array_slice($activities, 0, 5);
}

$welcome = dashboard_message($user);
$activities = fetch_latest_activities($db, $user);
?>
<main class="page container">
  <section class="card fade-in" style="margin-bottom: 24px;">
    <h1 class="section-title" style="margin-bottom: 8px;"><?= h($welcome['titulo']) ?></h1>
    <p class="muted" style="margin-bottom: 0;"><?= h($welcome['texto']) ?></p>
  </section>

  <section class="card fade-in" style="margin-bottom: 24px;">
    <h2 style="margin-bottom: 12px;">Últimas atividades</h2>

    <?php if ($activities): ?>
      <div class="activity-list">
        <?php foreach ($activities as $activity): ?>
          <div class="activity-item" style="padding: 12px 0; border-bottom: 1px solid #e9e9e9;">
            <strong><?= h($activity['label']) ?></strong>
            <p style="margin: 6px 0 4px;"><?= h($activity['text']) ?></p>
            <small class="muted"><?= h($activity['date']) ?></small>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="muted">Ainda não há atividades recentes para exibir.</p>
    <?php endif; ?>
  </section>

  <section class="cards-grid fade-in">
    <?php foreach ($cards as $card): ?>
      <article class="card<?= str_contains($card['page'], 'carta') ? ' carta' : '' ?>">
        <h2><?= h($card['title']) ?></h2>
        <p><?= h($card['desc']) ?></p>
        <a class="card-button" href="<?= h($card['page']) ?>">Abrir</a>
      </article>
    <?php endforeach; ?>
  </section>
</main>
<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>
