<?php
/**
 * Created by PhpStorm.
 * User: priyankanaik
 * Date: 07/10/2018
 * Time: 00:11
 */
    include_once 'header.php';
    include_once 'includes/getter.php';
    $games = getAllGame($conn);
    $game_types = getAllGameType($conn);
?>

    <section class="main-container">
        <div class="main-wrapper">
            <h2>Game</h2>
            <form class="game-form" action="includes/game.inc.php" method="post">
                <select name="select-game-type">
                    <?php
                    while($row = $game_types->fetch_assoc()) {
                        echo "<option value=" . $row['id'] . ">" . $row['name'] . "</option>";
                    }
                    ?>
                </select>
                <select name="select-game">
                    <option value="0">Select Game</option>
                    <?php
                        while($row = $games->fetch_assoc()) {
                            echo "<option value=" . $row['id'] . ">" . $row['name'] . "</option>";
                        }
                    ?>
                </select>
                <input type="text" name="game" placeholder="Enter Game">
                <button type="submit" name="add">Add</button>
                <button type="submit" name="update">Update</button>
                <button type="submit" name="delete">Delete</button>
            </form>
        </div>
    </section>

<?php
    include_once 'footer.php';
?>