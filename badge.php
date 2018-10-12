<?php
/**
 * Created by PhpStorm.
 * User: priyankanaik
 * Date: 07/10/2018
 * Time: 01:04
 */
    include_once 'header.php';
?>

    <section class="main-container">
        <div class="main-wrapper">
            <h2>Badge</h2>
            <form class="badge-form" action="includes/badge.inc.php" method="post">
                <select name="select-badge">
                    <option value="0">Select Badge</option>
                </select>
                <input type="text" name="badge" placeholder="Enter Badge">
                <button type="submit" name="add">Add</button>
                <button type="submit" name="update">Update</button>
                <button type="submit" name="delete">Delete</button>
            </form>
        </div>
    </section>

<?php
    include_once 'footer.php';
?>