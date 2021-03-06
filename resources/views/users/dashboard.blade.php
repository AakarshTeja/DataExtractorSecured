@include('include/header')

<div class="jumbotron">
    <div class="container">
        <h1> Hi @if(isset($_SESSION['name'])) {{ $_SESSION['name']}}@endif,<br> This is your Dashboard</h1>
    </div>
</div>
<main style="font-size:12px;" role="main" class="col">

  
     <div class="container" style="padding-bottom:1%;">     
        <div class="flash-message">
            @foreach (['danger', 'warning', 'success', 'info'] as $msg)
              @if(Session::has('alert-' . $msg))
              <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
              @endif
            @endforeach
          </div>
    <form action="/extracts" method="post" enctype="multipart/form-data">
      <h3>Select Files to Upload:</h3>
      <label>Please make sure doc files are converted to docx:</label>
      <div class="form-group row">
        <div class="col-md-6">
          <input style="height:40px; font-size:15px;" class="form-control" type="file" name="files[]" multiple="">
        </div>
        <div class="col-md-3">
          <input style="width:100%; height:40px; font-size:20px;" class="btn btn-info " type="submit" name="mul_submit" value="Upload & Extract">
        </div>
        <button class="btn" name='download' style="
            background-color: DodgerBlue;
            border: none;
            color: white;
            padding: 5px 25px;
            cursor: pointer;
            font-size: 20px;
            :hover {
            background-color: RoyalBlue;"><i class="fa fa-download"></i> Download</button>
      </div>
      @csrf
  </form>
  

      <?php
            // Include the database configuration file
            include "../database/connections/db.php";

            // Get images from the database
            $num_rows=0;
            $query = $conn->query("SELECT * FROM files ORDER BY id DESC");
            
            if($query){
            $num_rows=mysqli_num_rows($query);
            // return $num_rows;
            }
            if($num_rows > 0){
      ?>
      <div class="table-responsive">
        <table id="example" class="display" style="width:100%">
          <thead>
              <tr>
                  <th>Serial Number</th>
                  <th>upload Date</th>
                  <th>File Names</th>
              </tr>
          </thead>
          <tbody>
            <?php
            while($row = $query->fetch_assoc()){
                      // $imageURL = 'uploads/'.$row["file_name"]; ?>

            <tr>
                  <td>{{$row['id']}}</td>
                  <td>{{$row['uploaded_on']}}?></td>
                  <td><a href="#">{{$row['file_name']}}</a><br></td>
            </tr>
                  <?php } ?>
            </tbody>
            <tfoot>
              <tr>
                <th>Serial Number</th>
                  <th>upload Date</th>
                <th>File Names</th>
              </tr>
          </tfoot>
          </table>
        </div>  
        </div>
           <?php }else{ ?>
                <p>No file(s) found...</p>
            <?php } ?> 
        <div>
</main>
@include('include/footer');
    <script>
     $(document).ready(function() {
    $('#example').DataTable();
     } ); 
</script>


