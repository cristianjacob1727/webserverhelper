<!doctype html>
<html>
  <head>
      <link href="js/bootstrap-5.3.3-dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
      <script src="js/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
      <script type="text/javascript" src="js/jquery.js"></script>
      <script type="text/javascript">
        $(document).ready( function() {

            function showResponse( data )
            {
                $('#response').html( '200 OK ' + data.response );
            }

            function setConfiguration(ev)
            {
                ev.preventDefault();
                console.log('sending ... ' +  $('#files_to_replace').val() ) ;
                $.post('/', 
                {
                    'action' : 'set_config',
                    'jboss_home' : $('#jboss_home').val(),
                    'repository_home' : $('#repository_home').val(),
                    'files_to_replace' : $('#files_to_replace').val(),
                    'path' : $('#config_file').val(),
                    'local_files_path' : $('#local_files_path').val(),
                    'remote_files_path' : $('#remote_files_path').val()
                    
                }, function(data) {
                    return ( data );
                });
            }

            function setConfigFile(ev)
            {
                ev.preventDefault();
                return $.post('/', 
                {
                    'action' : 'set_config_file',            
                    'path' : $('#config_file').val()
                    
                });
            }


          $('#clean_jboss').on( 'click', function(ev) {
            ev.preventDefault();
            $.post('/', {'action' : 'clean_jboss'}, function(data) {
                showResponse( data );

            });
          });
  
          $('#refresh').on( 'click', function(ev) {
            ev.preventDefault();
            $.post('/', {'action' : 'refresh'}, function(data) {
                showResponse( data );

            });
          });

          $('#envlocal').on( 'click', function(ev) {
            ev.preventDefault();
            $.post('/', {'action' : 'point_to_local'}, function(data) {
                showResponse( data );

            });
          });

          $('#envremote').on( 'click', function(ev) {
            ev.preventDefault();
            $.post('/', {'action' : 'point_to_remote'}, function(data) {
                showResponse( data );

            });
          });

          $('#backup').on( 'click', function(ev) {
            ev.preventDefault();
            $.post('/', {'action' : 'backup_branch'}, function(data) {
                showResponse( data );
            });
          });

          $('input').on( 'click', function() {
            $('#response').html('Processing...');
          });


          $('.save_config').on( 'click', function(ev) {
            setConfiguration(ev);

          });

          $('#set_config_file').on( 'click', function(ev) {
            setConfigFile(ev).done( function(data) {
              var result = JSON.parse(data);
              $('#repository_home').val( result.repository_home );
              $('#jboss_home').val( result.jboss_home );
              $('#local_files_path').val( result.local_files_path );
              $('#remote_files_path').val( result.remote_files_path );
              var output = "";
              for (i = 0; i < result.files_to_replace.length; i++) {
                output += result.files_to_replace[i] + '\n';
              }
              $('#files_to_replace').val( output );

            });

          });

          $('#save').on( 'click', function(ev) {
            setConfiguration(ev);

          });

          $('#delete_targets').on( 'click', function(ev) {
            ev.preventDefault();
            $.post('/', {'action' : 'delete_targets'}, function(data) {
                showResponse( data );

            });

          });

        });
      </script>
        <style type="text/css">
        
        * {
            border-radius:5px;
            border-width:1px;
            padding:8px;
            font-family: Hack;
        }

        .widthText {
            width:400px;
        }

        textarea {
            display: block;
            width: 90% !important;
            height: 300px;
            text-wrap: nowrap;
        }

        input[type=button] {
            border:1px  outset;
        }

        input[type=button]:active {
            border:1px inset;
            border-top:1px solid green;
        }

        input[type=button]:hover {
            color:white;
            background-color: darkblue;
            cursor:pointer;
        }

        </style>
  </head>
  <body>

  <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">
      Respository Helper</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">
      Remote Debugger</button>
  </li>

</ul>
<div class="tab-content" id="pills-tabContent">
  <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">...</div>
  <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">...</div>
  <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">...</div>
</div>

        <h1>Web server and Repository Helper</h1>
      <form method="post" action="index.php" method="post" novalidate onsubmit="return submitForm();">

        <label for="config_file">Config file</label>
        <input id="config_file" class="widthText" name="config_file" type="text" value="<?php print $session->configFile; ?>"/>
        <input id="set_config_file"  name="set_config_file" type="button" value="Set"/><br/><br/>
      
        <label for="repository_folder">Repositorio</label>
        <input id="repository_home" name="repository_home"  class="widthText" type="text" value="<?php print $appConfig->repository_home; ?>"/>
        <input id="set_repository" class="save_config" name="set_repository" type="button" value="Set"/><br/><br/>
   
        <label for="jboss_home">Jboss Home</label>
        <input id="jboss_home" name="jboss_home"  class="widthText" type="text" value="<?php print $appConfig->jboss_home; ?>"/>
        <input id="set_jboss_home" class="save_config" name="set_jboss_home" type="button" value="Set"/><br/><br/>
   
        <label for="files_to_replace">Files to replace</label><br/>
        <textarea id="files_to_replace" name="files_to_replace" class="widthText"><?php print stringListToText( $appConfig->files_to_replace ); ?></textarea>
        <input id="set_files" class="save_config" name="set_files" type="button" value="Set"/><br/><br/>


        <label for="local_files_path">Point to LOCAL files path</label><br/>
        <input id="local_files_path" name="local_files_path"  class="widthText" type="text" value="<?php print $appConfig->local_files_path; ?>"/>
        <input id="set_local_files_path" class="save_config" name="set_local_files_path" type="button" value="Set"/><br/><br/>

        <label for="remote_files_path">Point to REMOTE files path</label><br/>
        <input id="remote_files_path" name="remote_files_path"  class="widthText" type="text" value="<?php print $appConfig->remote_files_path; ?>"/>
        <input id="set_remote_files_path" class="save_config" name="set_remote_files_path" type="button" value="Set"/><br/><br/>

        <input id="clean_jboss" name="clean_jboss" type="button" value="Clean Jboss"/>
        <input id="refresh" name="refresh" type="button" value="Refresh"/>
        <input id="envlocal" name="envlocal" type="button" value="Point to localhost"/>
        <input id="envremote" name="envremote" type="button" value="Point to Remote"/>
        <input id="backup" name="backup" type="button" value="Backup branch"/>
        <input id="save"  name="save" type="button" value="Save all"/>
        <input id="delete_targets"  name="delete_targets" type="button" value="Delete targets"/>

        <div id="response">
        </div>
          <?php /*
          $i = 0;
          foreach ($directoriesToDelete as $dir) { ?>
            <input type="submit" name="delete<?php print $i;  ?>" value="<?php print $dir; ?>"/>
          <?php } */ ?>
      </form>
  </body>
</html>