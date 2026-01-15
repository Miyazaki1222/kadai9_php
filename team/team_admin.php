<?php
require_once('../db_config.php');

// 権限設定のための定義（1:選手, 2:コーチ...）
$member_type = [
    1 => '選手',
    2 => 'コーチ',
    3 => '監督',
    4 => '元チームメンバー'
];

// チーム一覧取得
$teams_stmt = $pdo->query("SELECT * FROM teams ORDER BY id DESC");
$teams = $teams_stmt->fetchAll(PDO::FETCH_ASSOC);

// メンバー一覧取得（チーム名も結合して取得）
$sql = "SELECT m.*, t.team_name FROM members m 
        LEFT JOIN teams t ON m.team_id = t.id 
        ORDER BY t.id DESC, m.back_number ASC";
$members_stmt = $pdo->query($sql);
$members = $members_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>チーム・メンバー管理</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .table-admin { font-size: 0.9rem; }
        .card-header { font-weight: bold; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <span class="navbar-brand">チーム・メンバー管理</span>
        <a href="../order/order_kanban.php" class="btn btn-outline-light btn-sm">オーダー作成へ</a>
    </div>
</nav>

<div class="container">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">チーム新規登録</div>
                <form action="insert_team.php" method="POST" class="card-body">
                    <input type="text" name="team_name" class="form-control mb-2" placeholder="チーム名" required>
                    <button type="submit" class="btn btn-primary btn-sm w-100">チーム作成</button>
                </form>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">メンバー新規登録</div>
                <form action="../member/insert_member.php" method="POST" class="card-body">
                    <select name="team_id" class="form-select mb-2" required>
                        <option value="">所属チームを選択</option>
                        <?php foreach($teams as $t): ?>
                            <option value="<?=$t['id']?>"><?=$t['team_name']?></option>
                        <?php endforeach; ?>
                    </select>

                    <select name="member_type" class="form-select mb-2" required>
                        <option value="">役割を選択</option>
                        <?php foreach($member_type as $key => $val): ?>
                            <option value="<?=$key?>"><?=$val?></option>
                        <?php endforeach; ?>
                    </select>

                    <input type="text" name="player_name" class="form-control mb-2" placeholder="氏名" required>
                    
                    <input type="number" name="back_number" class="form-control mb-2" 
                           placeholder="背番号（半角数字）" min="0" step="1" required>
                    
                    <button type="submit" class="btn btn-success btn-sm w-100">メンバー追加</button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span>登録名簿一覧</span>
                    <span class="badge bg-secondary"><?=count($members)?> 名登録済み</span>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 table-admin">
                        <thead class="table-light">
                            <tr>
                                <th>チーム</th>
                                <th>役割</th>
                                <th>背番号</th>
                                <th>氏名</th>
                                <th class="text-center">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($members as $m): ?>
                            <tr>
                                <td><span class="badge bg-outline-secondary border text-dark"><?=$m['team_name']?></span></td>
                                <td>
                                    <?= htmlspecialchars($member_type[$m['member_type']] ?? '不明') ?>
                                </td>
                                <td><?=$m['back_number']?></td>
                                <td><strong><?=$m['player_name']?></strong></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="../member/edit_member.php?id=<?=$m['id']?>" class="btn btn-sm btn-outline-primary py-0">
                                            編集
                                        </a>
                                        <a href="../member/delete_member.php?id=<?=$m['id']?>" 
                                           class="btn btn-sm btn-outline-danger py-0"
                                           onclick="return confirm('本当に削除しますか？');">
                                            削除
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>