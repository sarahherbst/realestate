				</div>
				<!-- /.container-fluid -->

				<!-- Sticky Footer -->
				<footer class="sticky-footer">
					<div class="container my-auto">
						<div class="copyright text-center my-auto">
							<span><?php echo date('Y'); ?> &copy; FFE media</span>
						</div>
					</div>
				</footer>

			</div>
			<!-- /.content-wrapper -->

		</div>
		<!-- /#wrapper -->

		<!-- Scroll to Top Button-->
		<a class="scroll-to-top rounded" href="#page-top">
			<i class="fas fa-angle-up"></i>
		</a>

		<!-- Logout Modal -->
		<div class='modal fade' id='logoutModal' tabindex='-1' role='dialog' aria-labelledby='logoutModalLabel' aria-hidden='true'>
			<div class='modal-dialog' role='document'>
				<div class='modal-content'>
					<div class='modal-header'>
						<h5 class='modal-title' id='logoutModalLabel'>Ready to Leave?</h5>
						<button type='button' class='close' data-dismiss='modal' aria-label='Close'>
							<span aria-hidden='true'>&times;</span>
						</button>
					</div>
					<div class='modal-body'>
						Wählen Sie 'Logout', wenn Sie die aktuelle Session beenden wollen.
					</div>
					<div class='modal-footer'>
						<button type='button' class='btn btn-secondary' data-dismiss='modal'>Abbrechen</button>
						<a class='btn btn-primary' href='logout.php'>Logout</a>
					</div>
				</div>
			</div>
		</div>

		<!-- Bootstrap core JavaScript -->
		<script type="text/javascript" src="vendor/jquery/jquery.min.js" defer></script>
		<script type="text/javascript" src="vendor/bootstrap/js/bootstrap.bundle.min.js" defer></script>
		<!-- Core plugin JavaScript-->
		<script type="text/javascript" src="vendor/jquery-easing/jquery.easing.min.js" defer></script>
		<!-- Table JavaScript / Page level plugin JavaScript-->
		<script type="text/javascript" src="vendor/datatables/jquery.dataTables.js" defer></script>
		<script type="text/javascript" src="vendor/datatables/dataTables.bootstrap4.js" defer></script>
		<!-- Custom scripts for this template -->
		<script type="text/javascript" src="js/sb-admin.min.js" defer></script>
		<script type="text/javascript" src="js/sb-admin-datatables.min.js" defer></script>
		<script type="text/javascript" src="js/scripts.js" defer></script>
	</body>
</html>

<?php
	// DB-Connection schließen:
	mysqli_close($db);
?>