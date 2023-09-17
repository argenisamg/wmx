<?php
   include("conexion.php");
   if (session_status() == PHP_SESSION_NONE) {
      session_start();
  }
   if (!isset($_SESSION['user_name']) || !isset($_SESSION['user_role']) || !isset($_SESSION['id']) ) {    
      header("Location: ../index.php");
      exit();
	}

	$nombre = $_SESSION['user_name'];
	$idrol = $_SESSION['user_role'];
	$iduser = $_SESSION['id'];
		

?>
<!DOCTYPE html>
<html> 
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>STORAGE</title>		
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<link href="../css/amgstyle.css" rel="stylesheet">
		<link href="../css/librerias/bootstrap-5.1.3/css/bootstrap.min.css" rel="stylesheet">
		<link href="../css/insert.css" type="text/css" rel="stylesheet">		
		<link rel="stylesheet" type="text/css" href="../css/modal.css" />		
		<link href="../css/DataTables/datatables.css" type="text/css" rel="stylesheet">

		<style>
         .modal-body label {
         margin-bottom: 8px;
         }
         .modal-body input,
         .modal-body select {
         margin-bottom: 12px;
         }
      </style>

	</head>
	<body>
	
		<?php include_once("../resourse/header.php");?>
		<label id="user"></label>
		
	<div class="container">
	<main>	    
		<div class="flex-col-amg flex-center-amg">
			<div class="tabs">
				<button class="tablink text-white-amg" onclick="openPage('Page1')">Administration</button>
				<button class="tablink text-white-amg" onclick="openPage('Page2')">In & Out</button>        
				<button class="tablink text-white-amg" onclick="openPage('Page3')">Inventory</button>        
			</div>

			<div id="Page1" class="tabcontent" page="1">
				<div class="amg-container-form">
					<h1 class="text-white">Register Cable Form</h1>
					<form class="amg-form" id="registerCableForm">
						<label for="article_id" class="amg-label text-white-amg">Article:</label>
						<select class="inputs bg-input-amg text-white-amg" id="article_id" name="article_id" value="" required></select>
						<!-- <input type="text" class="inputs bg-input-amg text-white-amg" id="article" name="article" required> -->
						
						<label for="partnumber" class="amg-label text-white-amg">PN:</label>
						<input type="text" class="inputs bg-input-amg text-white-amg" id="partnumber" name="partnumber" required>

						<label for="serialnumber" class="amg-label text-white-amg">Serial Number:</label>
						<input type="text" class="inputs bg-input-amg text-white-amg" id="serialnumber" name="serialnumber">

						<label for="registerdate" class="amg-label text-white-amg">Register Date:</label>
						<input type="date" class="inputs bg-input-amg text-white-amg" id="registerdate" name="registerdate" required>

						<label for="descriptioncable" class="amg-label text-white-amg">Description:</label>
						<textarea class="inputs bg-input-amg text-white-amg" id="descriptioncable" name="descriptioncable" rows="4" required></textarea>

						<label for="quantity" class="amg-label text-white">Quantity:</label>
						<input type="number" class="inputs bg-input-amg text-white-amg" id="quantity" name="quantity" value="" required>

						<button type="reset" class="btn-amg bg-blue-amg text-white-amg text-med" name="resetform">Reset Form</button>
						<button type="submit" class="btn-amg bg-green-amg text-white-amg text-med" name="savenewregister">Save New Register</button>
					</form>
				</div>	
			</div>

			<div id="Page2" page="2" class="tabcontent">
				<div class="flex-row-amg gap-three">
					<div class="amg-container-form">
						<h1 class="text-white">In & Out Form</h1>
						<form class="amg-form" id="inoutForm">
							<label for="articleinout" class="amg-label text-white-amg">Select an Article:</label>
							<select class="inputs bg-input-amg text-white-amg" onchange="selectPartNumber(this);" name="articleinout" id="articleinout" value=""></select>
							<!-- <input type="text" class="inputs bg-input-amg text-white-amg" id="article" name="article" required> -->
							
							<label for="id_cable" class="amg-label text-white-amg">Part Number:</label>
							<select class="inputs bg-input-amg text-white-amg" onchange="selectSerialNumber(this);" id="id_cable" name="id_cable" value=""></select>							
							<!-- <input type="text" class="inputs bg-input-amg text-white-amg" id="partnumber" name="partnumber" value="" required> -->
							
							<label for="serialnumber2" class="amg-label text-white-amg" id="labelSN2">Serial Number:</label>
							<select class="inputs bg-input-amg text-white-amg" id="serialnumber2" name="serialnumber2" value=""></select>
							<!-- <input type="text" class="inputs bg-input-amg text-white-amg" id="serialnumber2" name="serialnumber2" value="" required> -->
							
							<label for="id_persona" class="amg-label text-white-amg">PIC:</label>
							<input type="text" class="inputs bg-input-amg text-white-amg" id="id_persona" name="id_persona" value="" required>
							
							<label for="fecha_prestamo" class="amg-label text-white-amg">Date Out:</label>
							<input type="date" class="inputs bg-input-amg text-white-amg" id="fecha_prestamo" name="fecha_prestamo" value="" required>
							
							<label for="fecha_devolucion" class="amg-label text-white-amg" hidden>Date In:</label>
							<input type="date" class="inputs bg-input-amg text-white-amg" id="fecha_devolucion" name="fecha_devolucion" value="" hidden>
							
							<!-- <label for="descriptioncable" class="amg-label text-white-amg">Description:</label>
							<textarea class="inputs bg-input-amg text-white-amg" id="descriptioncable" name="descriptioncable" rows="4" required></textarea> -->
							
							<label for="quantity_prestamo" class="amg-label text-white">Output Quantity:</label>
							<input type="number" class="inputs bg-input-amg text-white-amg" id="quantity_prestamo" name="quantity_prestamo" value="" required>
							
							<button type="reset" class="btn-amg bg-blue-amg text-white-amg text-med" name="resetform2">Reset Form</button>
							<button type="submit" class="btn-amg bg-green-amg text-white-amg text-med" name="saveoperation">Save Operation</button>
						</form>
					</div>	
					
						<div class="flex-col-amg flex-between-amg">													
							<div class="flex-col-amg gap-one"> <!--something-->
								<div>
									<form id="loanForm">
										<div class="flex-row-amg flex-center-amg gap-one">
											<div class="flex-col-amg gap-one">
												<!-- <label for="pic" class="amg-label text-black-amg">Search material:</label> -->
												<input type="text" class="inputs" id="picsnpn" name="picsnpn" placeholder="PIC here..." required>
											</div>
											<div>
												<button type="submit" class="btn-amg bg-black-amg text-white-amg">Search</button>
											</div>
										</div>
									</form>
								</div>
								<div>									
									<table id="tableBorrowed" class="table-amg">
										<thead id="dataHead">
											<th>TOTAL</th>
											<th>PIC</th>
											<th>ARTICLE</th>
											<th>PN</th>
											<th>SN</th>										
											<th>DATE OUT</th>
											<th>DATE IN</th>										
											<th>QUANTITY BORROWED</th>
											<th>ACTIONS</th>
										</thead>
										<tbody id="dataBodyPage2"></tbody>
									</table>													
								</div>
							</div> <!--something-->
							<div class="span-amg"></div>
						</div>
					
				</div>
			</div>
				
			<div id="Page3" page="3" class="tabcontent">
				<div>
					<h3>This table shows the current inventory:</h3>
				</div>
				<div class="divrow">
					<div class="flex-col-amg flex-center-amg gap-two">		
						<span></span>				
						<table id="dataCable" class="table-amg">
							<thead id="dataHead">
								<th>#</th>
								<th>ARTICLE</th>
								<th>PN</th>
								<th>SN</th>
								<th>REGISTER DATE</th>
								<th>DESCRIPTION</th>								
								<th>QUANTITY</th>
								<th>BORROWED</th>
								<th>ACTIONS</th>
							</thead>
							<tbody id="tbodycontent"></tbody>
						</table>
					</div>
				</div>	
				<?php include_once("../phpamg/modal.php");?>			
			</div>

		</div>		
	</main>
	</div>
	
	<!-- <?php include_once("../resourse/footer.php");?> -->
	<!-- SCRIPTS -->		
	<script type="text/javascript" src="../js/fiberncables.js"></script>
	<!-- DataTables -->
    <script type="text/javascript" src="../phpamg/DataTables/jQuery-3.5.1.js"></script>
    <script type="text/javascript" src="../phpamg/DataTables/jquery.dataTables.min.js"></script>
	
	<script src="../css/librerias/bootstrap-5.1.3/js/bootstrap.bundle.min.js"></script>
	<script src="../js/pagination.js"></script>	
	
	</body>
</html>