<?php
function endl() { return "\n"; }
for ($i = 1; $i <= 9; $i++) {
  print 'jboss-cli.bat "patch apply C:\Tools\Jboss72\jboss-eap-7.2.' . $i . '-patch.zip"' . endl();
}

?>
