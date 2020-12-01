<?php

function it($m,$p){is_callable($p) && $p = $p();echo ($p?'✔︎':'✘')." It $m\n"; if(!$p){$GLOBALS['f']=1;}}function done(){if(@$GLOBALS['f'])die(1);}