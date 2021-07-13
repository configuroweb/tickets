<?php
$view = isset($_GET['view']) ? $_GET['view'] : 'index';
switch ($view) {
	case 'create_ticket':
		include(base_app.'admin/ticket/manage_ticket.php');
		break;
	case 'edit':
			include(base_app.'admin/ticket/manage_ticket.php');
			break;
	case 'ticket_list':
		include(base_app.'admin/ticket/ticket_list.php');
		break;
	case 'view_ticket':
		include(base_app.'admin/ticket/ticket.php');
		break;
	default:
		include(base_app.'admin/ticket/ticket_list.php');
		break;
}
?>