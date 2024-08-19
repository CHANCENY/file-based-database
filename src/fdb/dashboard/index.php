<?php
@session_start();

global $root_path;
global $database_name;
global $database_username;
global $database_password;
global $database;

/**@var $dashboard_database \Fdb\Connect\Connect **/
$dashboard_database = $database;

$collections = $dashboard_database?->getCollections() ?? [];

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Database Dashboard</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
    <style>
        <?= file_get_contents(__DIR__.'/assets/main.css') ?>
    </style>
    <script src="https://cdn.jsdelivr.net/npm/json2html.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

<!------ Include the above in your HEAD tag ---------->
<link href="http://fontawesome.io/assets/font-awesome/css/font-awesome.css" rel="stylesheet" media="screen">

<nav class="navbar navbar-default navbar-fixed-top topbar">
    <div class="container-fluid">
        <div class="navbar-header">
            <a href="#" class="navbar-brand">
                <span class="visible-xs">FDB</span>
                <span class="hidden-xs"><?= $database_name ?></span>
            </a>
            <p class="navbar-text">
                <a href="#" class="sidebar-toggle">
                    <i class="fa fa-bars"></i>
                </a>
            </p>

        </div>

        <div class="navbar-collapse collapse" id="navbar-collapse-main">

            <ul class="nav navbar-nav navbar-right">
                <li>
                    <button class="navbar-btn">
                        <i class="fa fa-bell"></i>
                    </button>
                </li>
            </ul>

        </div>
    </div>
</nav>
<article class="wrapper">
    <aside class="sidebar">
<!--        <ul class="sidebar-nav">-->
<!--            <li class="active"><a href="#dashboard" data-toggle="tab"><i class="fa fa-dashboard"></i> <span>Dashboard</span></a></li>-->
<!--            <li><a href="#configuration" data-toggle="tab"><i class="fa fa-cogs"></i> <span>Configuration</span></a></li>-->
<!--            <li><a href="#users" data-toggle="tab"><i class="fa fa-users"></i> <span>Users</span></a></li>-->
<!--            <li><a href="#mail" data-toggle="tab"><i class="fa fa-envelope"></i> <span>Mail</span></a></li>-->
<!--        </ul>-->
    </aside>
    <section class="main">
        <section class="tab-content">
            <section class="tab-pane active fade in content" id="dashboard">
                <div class="row">

                    <div class="col-xs-12 col-sm-3">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Collections
                            </div>
                            <div class="panel-body">
                                <div>
                                    <?php  foreach ($collections as $col=>$collection): ?>
                                       <details>
                                           <summary><?= $col ?></summary>
                                           <?php foreach ($collection['keys'] as $key): ?>
                                              <li><a href="#<?= $key ?>" class="collection-name"><?= $key ?></a></li>
                                           <?php endforeach; ?>
                                       </details>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-9">
                        <?php if(empty($database)): ?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Database Credentials
                            </div>
                            <div class="panel-body">
                                <div class="col-md-10 mt-auto">
                                    <form method="post" action="#" class="form">
                                       <div class="row">
                                           <div class="col-md-5">
                                               <div class="form-group">
                                                   <label for="database_name">Database Name</label>
                                                   <input name="database_name" required placeholder="enter database name" type="text" class="form-control" id="database_name">
                                                   <span class="text-body-tertiary">Database name</span>
                                               </div>
                                               <div class="form-group">
                                                   <label for="username">Username</label>
                                                   <input name="username" required placeholder="enter username" type="text" class="form-control" id="username">
                                                   <span class="text-body-tertiary">username</span>
                                               </div>
                                               <div class="form-group">
                                                   <label for="password">Password</label>
                                                   <input name="password" required placeholder="enter password" type="password" class="form-control" id="password">
                                                   <span class="text-body-tertiary">password</span>
                                               </div>
                                               <div class="form-group">
                                                   <input type="submit" value="Submit" name="submit" class="btn btn-primary">
                                               </div>
                                           </div>
                                       </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                          <div class="panel panel-default">
                              <div class="panel-heading">
                                  Data table of collection
                              </div>
                              <div class="panel-body">
                                  <div class="col-md-10 mt-auto" id="collection-data">
                                  </div>
                              </div>
                          </div>
                        <?php endif; ?>
                    </div>

                </div>

            </section>
        </section>
    </section>
</article>

<div class="modal-hidden modal" itemtype="collection-key-modal">
    <div class="modal-header">
        <h3>Collection key</h3>
        <label for="modal">
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAAAdVBMVEUAAABNTU1NTU1NTU1NTU1NTU1NTU1NTU1NTU1NTU1NTU1NTU1NTU1NTU1NTU1NTU1NTU1NTU1NTU1NTU1NTU1NTU1NTU1NTU1NTU1NTU1NTU1NTU1NTU1NTU1NTU1NTU1NTU1NTU1NTU1NTU1NTU1NTU1NTU0N3NIOAAAAJnRSTlMAAQIDBAUGBwgRFRYZGiEjQ3l7hYaqtLm8vsDFx87a4uvv8fP1+bbY9ZEAAAB8SURBVBhXXY5LFoJAAMOCIP4VBRXEv5j7H9HFDOizu2TRFljedgCQHeocWHVaAWStXnKyl2oVWI+kd1XLvFV1D7Ng3qrWKYMZ+MdEhk3gbhw59KvlH0eTnf2mgiRwvQ7NW6aqNmncukKhnvo/zzlQ2PR/HgsAJkncH6XwAcr0FUY5BVeFAAAAAElFTkSuQmCC" width="16" height="16" alt="">
        </label>
    </div>
    <table class="table table-stripped">
        <thead>
           <tr>
               <td>Key</td>
               <td>Type</td>
               <td>Primary</td>
               <td>Unique</td>
               <td></td>
           </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>




<div style="display: none;" id="collections"><?= json_encode($dashboard_database->getCollections(), JSON_PRETTY_PRINT); ?></div>
<script>
    <?php echo file_get_contents(__DIR__.'/assets/main.js') ?>

    DashboardBehavior.init();
</script>
</body>
</html>
