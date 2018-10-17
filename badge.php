<?php
/**
 * Created by PhpStorm.
 * User: priyankanaik
 * Date: 07/10/2018
 * Time: 01:04
 */
    include_once 'header.php';
    include_once 'includes/getter.php';
    $badges = getAllBadge($conn);
?>

    <section class="main-container">
        <div class="main-wrapper">
            <h2>Badge</h2>
            <form class="badge-form" action="includes/badge.inc.php" method="post" enctype="multipart/form-data">
                <input type="text" id="badge-name" name="badge-name">
                <input type="file" id="badge" name="badge"/>
                <select name="select-badge">
                    <option value="0">Select Badge</option>
                    <?php
                        while($row = $badges->fetch_assoc()) {
                            echo "<option value=" . $row['id'] . " data-subtext=\"<img width='2%' height='1%' src='". $row['badge'] . "'/>\"></option>";
                        }
                    ?>
                </select>
                <textarea name="badge" placeholder="Enter Description"></textarea>
                <button type="submit" name="add">Add</button>
                <button type="submit" name="update">Update</button>
                <button type="submit" name="delete">Delete</button>
                <div>
<!--                    --><?php
//                        while($row = $badges->fetch_assoc()) {
//                            echo '<img src="' . $row['badge'] .'" />';
//                        }
//                    ?>
                </div>
            </form>
        </div>
    </section>

<?php
    include_once 'footer.php';
?>