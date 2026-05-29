<?php
    require_once "../../../includes/auth.php";
?>

<!DOCTYPE html>
<html>
    <head>
        <title>DOM XSS (Fixed Style)</title>
        <link rel="stylesheet" href="../../../assets/css/style.css">
    </head>

    <body>
        <?php require_once "../../../includes/sidebar.php"; ?>
        <div class="content">
            <h1>DOM XSS</h1>
            <h2>Hãy chọn sách bạn muốn</h2>
            <form name="allbook" method="GET">
                <select name="book" id="book-select">
                    <option value="Foxie">Foxie</option>
                    <option value="Pophello">Pophello</option>
                    <option value="Naru">Naru</option>
                </select>
                <input type="submit" style="margin-top: 20px;" value="Chọn">
            </form>

            <div id="output" style="margin-top: 20px;"></div>

            <script>
                const urlParams = new URLSearchParams(window.location.search);
                const bookParam = urlParams.get('book');
                const selectElement = document.getElementById("book-select");

                if (bookParam) {
                    const newOption = document.createElement("option");
                    newOption.value = bookParam;
                    newOption.text = bookParam;
                    newOption.selected = true;
                    selectElement.insertBefore(newOption, selectElement.firstChild);
                } else {
                    const defaultOption = document.createElement("option");
                    defaultOption.value = "OhMyCrab";
                    defaultOption.text = "OhMyCrab";
                    defaultOption.selected = true;
                    selectElement.insertBefore(defaultOption, selectElement.firstChild);
                }
            </script>
        </div>
        <div class="content">
            <button>
                <a href="/OhMyCrab/modules/xss/dom.php">
                    Back to lab
                </a>
            </button>
        </div>
    </body>
</html>