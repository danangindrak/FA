<?php

$page_security = 1;
$path_to_root="../..";

include_once($path_to_root . "/purchasing/includes/purchasing_db.inc");
include_once($path_to_root . "/includes/session.inc");

include_once($path_to_root . "/purchasing/includes/purchasing_ui.inc");

$js = "";
if ($use_popup_windows)
	$js .= get_js_open_window(900, 500);
page(_("View Supplier Credit Note"), true, false, "", $js);

if (isset($_GET["trans_no"]))
{
	$trans_no = $_GET["trans_no"];
}
elseif (isset($_POST["trans_no"]))
{
	$trans_no = $_POST["trans_no"];
}

$supp_trans = new supp_trans();
$supp_trans->is_invoice = false;

read_supp_invoice($trans_no, 21, $supp_trans);

display_heading(_("SUPPLIER CREDIT NOTE") . " # " . $trans_no);
echo "<br>";
start_table($table_style2);
start_row();
label_cells(_("Supplier"), $supp_trans->supplier_name, "class='tableheader2'");
label_cells(_("Reference"), $supp_trans->reference, "class='tableheader2'");
label_cells(_("Supplier's Reference"), $supp_trans->supp_reference, "class='tableheader2'");
end_row();
start_row();
label_cells(_("Invoice Date"), $supp_trans->tran_date, "class='tableheader2'");
label_cells(_("Due Date"), $supp_trans->due_date, "class='tableheader2'");
label_cells(_("Currency"), get_supplier_currency($supp_trans->supplier_id), "class='tableheader2'");
end_row();
comments_display_row(21, $trans_no);
end_table(1);

$total_gl = display_gl_items($supp_trans, 3);
$total_grn = display_grn_items($supp_trans, 2);

$display_sub_tot = number_format2($total_gl+$total_grn,user_price_dec());

start_table("$table_style width=95%");
label_row(_("Sub Total"), $display_sub_tot, "align=right", "nowrap align=right width=17%");

$tax_items = get_supp_invoice_tax_items(21, $trans_no);
display_supp_trans_tax_details($tax_items, 1);

$display_total = number_format2(-($supp_trans->ov_amount + $supp_trans->ov_gst),user_price_dec());
label_row(_("TOTAL CREDIT NOTE"), $display_total, "colspan=1 align=right", "nowrap align=right");

end_table(1);

$voided = is_voided_display(21, $trans_no, _("This credit note has been voided."));

if (!$voided)
{
	display_allocations_from(payment_person_types::supplier(), $supp_trans->supplier_id, 21, $trans_no, -($supp_trans->ov_amount + $supp_trans->ov_gst));
}

end_page(true);

?>