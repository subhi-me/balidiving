<?php
   // Set the new location
   $location = "https://www.google.com/search?q=balidiving.com&sca_esv=458fc5d25ecd7a59&rlz=1C1CHBD_enID945ID945&sxsrf=AHTn8zrVR2nMl5Ee38aI6lsedtYdQd6omA%3A1742008283132&ei=2-_UZ9HPB4eSseMP_P3dmAg&ved=0ahUKEwiRqaXHjouMAxUHSWwGHfx-F4MQ4dUDCBA&uact=5&oq=balidiving.com&gs_lp=Egxnd3Mtd2l6LXNlcnAiDmJhbGlkaXZpbmcuY29tSIQwULcSWKQscAF4AZABAJgBgAGgAf4DqgEDNS4xuAEDyAEA-AEBmAIEoALOAsICBxAjGLADGCfCAgoQABiwAxjWBBhHwgINEAAYgAQYsAMYQxiKBcICHBAuGIAEGLADGEMYxwEYyAMYigUYjgUYrwHYAQHCAhkQLhiABBiwAxhDGMcBGMgDGIoFGK8B2AEBwgITEC4YgAQYsAMYQxjIAxiKBdgBAcICDhAAGLADGOQCGNYE2AEBwgIHECMYsQIYJ8ICCxAAGIAEGJECGIoFwgIGEAAYBxgewgINEC4YgAQYxwEYChivAcICBxAAGIAEGArCAhAQLhiABBjHARgKGI4FGK8BwgIGEAAYDRgemAMAiAYBkAYTugYGCAEQARgIkgcDMy4xoAeTIw&sclient=gws-wiz-serp";
 
   // Redirect the user with a 301 status code
   header("HTTP/1.1 301 Moved Permanently");
   header("Location: $location");
   exit;
?>