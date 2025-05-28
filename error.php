<!DOCTYPE html>
<html lang="en">
<?php
  if ($_GET['error'] == 'tooShort') {
      echo "Error 400: Bitte geben Sie in der Suchleiste mindestens 3 Zeichen ein";
  }

if ($_GET['error'] == 'invalidParam') {
    echo "Error 400: Der übergebene Parameter ist nicht gültig";
}

if ($_GET['error'] == "missingParam") {
    echo "Error 400: Bitte übergib einen gültigen Parameter";
}

if ($_GET['error'] == 'invalidID') {
    if (isset($_GET['type']) && $_GET['type'] === 'subject') {
        echo "Error 404: Es konnte kein Thema mit der angegebenen ID gefunden werden";
    } elseif (isset($_GET['type']) && $_GET['type'] === 'artist') {
        echo "Error 404: Es konnte kein Künstler mit der angegebenen ID gefunden werden";
    } else {
        echo "Error 404: Es konnte kein Eintrag mit der angegebenen ID gefunden werden";
    }
}
?>
</html>