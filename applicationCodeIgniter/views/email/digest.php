<p style="font-size: 13px; margin: 10px 0;"><span style="color:#444444">Hello <?php echo $user["first_name"]; ?>, </span><br/>Here's what's happened in the last 24 hours.</p><br/>
<?php foreach ($projects as $id => $project) { ?>
	<a href="<?php echo base_url() . $id; ?>" style="font-size: 16px;"><?php echo $project["name"]; ?></a>
	<?php foreach ($project["user_activity"] as $user) {
    echo "<ul>";
    if (isset($user["revisions"])) {
        $phrase = $user["revisions"] == 1 ? "%s submitted 1 revision" : "%s submitted %s revisions";
        echo "<li>" . sprintf($phrase, "<b>" . $user["name"] . "</b>", $user["revisions"]) . "</li>";
    }
    if (isset($user["error"])) {
        $phrase = $user["error"] == 1 ? "%s requested 1 change" : "%s requested %s changes";
        echo "<li>" . sprintf($phrase, "<b>" . $user["name"] . "</b>", $user["error"]) . "</li>";
    }
    if (isset($user["approved"])) {
        $phrase = $user["approved"] == 1 ? "%s approved 1 translation" : "%s approved %s translations";
        echo "<li>" . sprintf($phrase, "<b>" . $user["name"] . "</b>", $user["error"]) . "</li>";
    }
    echo "</ul><br/>";
} ?>
<?php } ?>