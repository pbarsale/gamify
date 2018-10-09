<?php
/**
 * Created by PhpStorm.
 * User: priyankanaik
 * Date: 04/10/2018
 * Time: 21:29
 */
    include_once 'header.php';
?>

<section class="main-container">
    <div class="main-wrapper">
        <h2>Game Type</h2>
        <form class="add-game-type-form" action="includes/add-game-type.inc.php" method="post">
            <select name="select-game-type">
                <option>Select Game</option>
            </select>
            <input type="text" name="game-type" placeholder="Enter Game Type">
            <button type="submit" name="add">Add</button>
            <button type="submit" name="update">Update</button>
            <button type="submit" name="delete">Delete</button>
        </form>
    </div>
</section>

<?php
    include_once 'footer.php';
?>


