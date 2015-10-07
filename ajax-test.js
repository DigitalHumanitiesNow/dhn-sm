function myAjax() {
      $.ajax({
           type: "POST",
           url: 'http://local.wordpress.dev/wp-content/plugins/dhn-sm/index.php',
           data:{action:'call_this'},
           success:function(html) {
             alert(html);
           }

      });
 }