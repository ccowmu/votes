<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>CCoWMU Votes</title>
  <style>
    body {
      max-width: 20em;
      margin: 0 auto;
    }
    input[type="text"],
    input[type="password"]  {
      display: block;
    }
    input[type="submit"] {
      width: 100%;
      margin: 1em 0;
    }
  </style>
</head>
<body>
<?php
function clean_username($s) {
  return preg_replace("/[^a-zA-Z0-9]/", "", $s);
}
function not_dot($s) {
  return !($s === "." or $s === "..");
}
function no_vote($s, $position_file) {
  echo "<dd>no vote</dd>";
  file_exists($position_file) and unlink($position_file);
}
if ($_SERVER['REQUEST_METHOD'] === "POST"):
  is_string($_POST['username']) and is_string($_POST['password']) or die("invalid POST parameters");
  $ldap = ldap_connect("localhost") or die("failed to connect to ldap");
  ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3) or die("failed to set protocol");
  $user = clean_username($_POST['username']);
  $binddn = "uid=$user,cn=members,dc=yakko,dc=cs,dc=wmich,dc=edu";
  $pass = $_POST['password'];
  if ($user and $pass and $bind = ldap_bind($ldap, $binddn, $pass)) {
    $votes_dir = 'votes/' . $user;
    echo "<p>Thank you for your vote, $user!</p>";
    echo "<dl>";
    file_exists($votes_dir) or mkdir($votes_dir, 0777, true);
    foreach (array_filter(scandir("positions"), 'not_dot') as $position) {
      $position_file = $votes_dir . '/' . $position;
      echo "<dt>$position</dt>";
      if (!array_key_exists($position, $_POST)
          or !is_string($_POST[$position])
          or $_POST[$position] === "") {
        no_vote($position_file);
        continue;
      }
      $vote = clean_username($_POST[$position]);
      $possible = array_map('trim', file('positions/' . $position));
      if (in_array($vote, $possible, true)) {
        echo "<dd>$vote</dd>";
        file_put_contents($position_file, $vote . "\n");
      } else {
        no_vote($position_file);
      }
    }
    echo "</dl>";
  } else {
    echo "<p>Login failed; go back in your browser history and try again.</p>";
  }
else: ?>
<h1>CCoWMU Votes</h1>
<form method="POST">
<?php foreach (array_filter(scandir("positions"), 'not_dot') as $position): ?>
  <fieldset>
    <legend><?php echo $position ?></legend>
  <?php foreach (array_map('trim', file('positions/' . $position)) as $candidate): ?>
    <div>
    <input type="radio" name="<?php echo $position ?>" value="<?php echo $candidate ?>">
      <label for="<?php echo $candidate ?>"><?php echo $candidate ?></label>
    </div>
  <?php endforeach; ?>
  </fieldset>
<?php endforeach; ?>
<div>
  <label for="username">username</label>
  <input type="text" name="username">
</div>
<div>
  <label for="password">password</label>
  <input type="password" name="password">
</div>
<input type="submit" value="cast vote">
</form>
<?php endif; ?>
</body>
</html>
