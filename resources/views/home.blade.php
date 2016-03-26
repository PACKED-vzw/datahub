
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="The Datahub project - prototype">

    <title>Datahub</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap theme -->
    <link href="css/bootstrap-theme.min.css" rel="stylesheet">
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="../../assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body role="document">

    <!-- Fixed navbar -->
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="#">Datahub</a>
        </div>
      </div>
    </nav>

    <div class="container theme-showcase" role="main">

      <!-- Main jumbotron for a primary marketing message or call to action -->
      <div class="row">
        <div class="jumbotron">
          <h1>Datahub</h1>
          <p>The Datahub is a software component which enables data managers in museums to exchange collection information between systems and applications in a flexible fashion via RESTful API's as LIDO XML or JSON formatted records.</p>
        </div>
      </div>

      <div class="row">
        <div class="alert alert-warning">
          <strong>Warning!</strong> This is a prototype version. Do not use in a production environment
        </div>
        <h1>Introduction</h1>
        <p>Available web services are listed below. Services accept or return JSON by default unless otherwise indicated. <a href="https://en.wikipedia.org/wiki/Cross-origin_resource_sharing">Cross-Origin Resource Sharing (CORS)</a> is disabled for all requests.</p>
      </div>

      <div class="row">
        <h2>Records</h2>

        <table class="table">
          <thead>
            <tr>
              <th>API call</th>
              <th>Description</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="col-md-3">POST record </td>
              <td>Create a new record. Used to ingest a record in the datahub.</td>
            </tr>
            <tr>
              <td class="col-md-3">GET record/{id}</td>
              <td>Returns a single LIDO record as a JSON formatted object. The LIDO record is represented in <a href="http://www.jclark.com/xml/xmlns.htm">Clark notation</a>. Accepts the uuid of the record as a parameter</td>
            </tr>
            <tr>
              <td class="col-md-3">GET record/{id}.xml</td>
              <td>Returns a single LIDO record as an XML formatted object. Accepts the uuid of the record as a parameter</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="row">
        <h2>Collections</h2>

        <table class="table">
          <thead>
            <tr>
              <th>API call</th>
              <th>Description</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="col-md-3">POST collection </td>
              <td>Create a new collection. Used to ingest a collection in the datahub.</td>
            </tr>
            <tr>
              <td class="col-md-3">GET collection </td>
              <td>Returns all available collections in the datahub.</td>
            </tr>
            <tr>
              <td class="col-md-3">GET collection/{id}</td>
              <td>Returns a single collection as a JSON formatted object. Accepts the id of the record as a parameter</td>
            </tr>
            <tr>
              <td class="col-md-3">PUT collection/{id}</td>
              <td>Update a single collection as a JSON formatted object. Accepts the id of the record as a parameter</td>
            </tr>
            <tr>
              <td class="col-md-3">DELETE collection/{id}</td>
              <td>Delete  a single collection as a JSON formatted object. Accepts the id of the record as a parameter</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="row">
        <hr />
        <p>This software is build and maintained by the Flemish Art Collection. This software is released as open source under a GPL v3 license.</p>
      </div

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="../../dist/js/bootstrap.min.js"></script>
    <script src="../../assets/js/docs.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
