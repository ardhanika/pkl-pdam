<html>
<head>
	<script type="text/javascript">
		function ConfirmDialog() {
			var x=confirm("Are you sure to delete record?")
			if (x) {
				return true;
			} else {
				return false;
			}
		}
	</script>
</head>
<body>	


<div class="table-responsive" style="padding-top: 6px">
	<table class="table table-striped table-hover table-condensed" id="mytable" style="width: 100%">
		<thead>
			<tr class="active">
				<th class="text-center" width="30px" style="padding-left: 20px;">No</th>
				<th>Proposal</th>
				<th>Surat Pengantar</th>
				<th>Topik</th>
				<th>Instansi</th>
				<th>Jurusan</th>
				<th>Prodi</th>
				<th class="text-center" width="160px" style="padding-left: 20px;">Aksi</th>
			</tr>
		</thead>
		<tbody>
			<?php $no = 1;
            foreach ($pengajuans as $pengajuan) { ?>
				<tr>
					<td class="text-center" width="30px" style="padding-left: 20px;"><?= $no++ ?></td>
					<td><?= $pengajuan->proposal ?></td>
					<td><?= $pengajuan->surat_pengantar ?></td>
					<td><?= $pengajuan->topik ?></td>
					<td><?= $pengajuan->asal ?></td>
					<td><?= $pengajuan->jurusan ?></td>
					<td><?= $pengajuan->prodi ?></td>
					<!-- <td><?= $this->session->userdata('status_pendaftaran') ?></td> -->
					<td class="text-center" width="160px" style="padding-left: 20px;">
						<?php echo anchor('member/pengajuan/detail/' .$pengajuan->pengajuan_id, 'Detail'); ?>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
</div>

</body>
</html>