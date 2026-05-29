<?php
    require_once "../../../includes/auth.php";
    require_once "../../../includes/connect.php";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $username = $_POST['username'];
        $comment = $_POST['comment'];

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO xss_stored_comments
            (
                username,
                comment
            )
            VALUES
            (
                ?, ?
            )"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ss",
            $username,
            $comment
        );

        mysqli_stmt_execute($stmt);
    }

    $result = mysqli_query(
        $conn,
        "SELECT * FROM xss_stored_comments
        ORDER BY id DESC"
    );
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Stored XSS</title>
        <link rel="stylesheet" href="../../../assets/css/style.css">
    </head>
    <body>
        <?php require_once "../../../includes/navbar.php"; ?>
        <?php require_once "../../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>Stored XSS</h1>
            <form method="POST">
                <input type="text" name="username" placeholder="Username">
                <br><br>
                <textarea name="comment" placeholder="Comment"></textarea>
                <br><br>
                <button type="submit">Post</button>
            </form>
            <hr>
            <h2>Comments</h2>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="comment">
                <strong>
                    <?php
                        echo htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8');
                    ?>
                </strong>
                <br>
                <?php
                    echo htmlspecialchars($row['comment'], ENT_QUOTES, 'UTF-8');
                ?>
            </div>
            <hr>
            <?php endwhile; ?>
            <a href="reset.php">Reset Comments</a>
        </div>
        <div class="content">
            <button>
                <a href="/OhMyCrab/modules/xss/stored.php">
                    Back to lab
                </a>
            </button>
        </div>
    </body>
</html>