<?php
/* Copyright (C) 2010-2013	Regis Houssin		<regis.houssin@inodbox.com>
 * Copyright (C) 2010-2011	Laurent Destailleur	<eldy@users.sourceforge.net>
 * ... (licence identique)
 */

if (empty($object) || !is_object($object)) {
	print "Error, template page can't be called as URL";
	exit(1);
}

global $filtertype, $forceall, $langs, $conf, $user;

if (empty($forceall)) $forceall = 0;

$coldisplay = 0;
print "<!-- BEGIN PHP TEMPLATE expedition/tpl/objectline_view.tpl.php -->\n";
print '<tr id="row-'.$line->id.'" class="drag drop oddeven" data-element="'.$line->element.'" data-id="'.$line->id.'" data-qty="'.$line->qty.'" data-product_type="'.$line->product_type.'">';

// Line nb
if (getDolGlobalString('MAIN_VIEW_LINE_NUMBER')) {
	print '<td class="linecolnum center">'.($i + 1).'</td>';
	$coldisplay++;
}

// Product / Description
print '<td class="linecoldescription line minwidth300imp tdoverflowmax300">';
print '<div id="line_'.$line->id.'"></div>';
$coldisplay++;
$tmpproduct = new Product($object->db);
$tmpproduct->fetch($line->fk_product);
if ($line->fk_product > 0) {
	print $tmpproduct->getNomUrl(1) . ' - ' . $tmpproduct->label;
} else {
	print ' - ' . $line->description;
}
print '</td>';

// Qty
print '<td class="linecolqty nowrap right">';
$coldisplay++;
echo price($line->qty, 0, '', 0, 0);
print '</td>';

// Batch
print '<td class="linecolbatch nowrap">';
$coldisplay++;
if (isModEnabled('productbatch')) {
    if (empty($line->detail_batch) || !is_array($line->detail_batch)) {
        require_once DOL_DOCUMENT_ROOT.'/expedition/class/expeditionlinebatch.class.php';
        $batchline = new ExpeditionLineBatch($object->db);
        $line->detail_batch = $batchline->fetchAll($line->id);
    }
    if (!empty($line->detail_batch) && is_array($line->detail_batch)) {
        $batchinfo = '';
        foreach ($line->detail_batch as $b) {
            if (!empty($b->batch)) {
                $batchinfo .= $b->batch . ' ('.$b->qty.')<br>';
            }
        }
        print $batchinfo ? trim($batchinfo) : $langs->trans("NA");
    } else {
        print $langs->trans("NA");
    }
} else {
    print $langs->trans("NA");
}
print '</td>';

// Entrepôt
print '<td class="linecolentrepot nowrap">';
$coldisplay++;
if ($line->fk_entrepot > 0) {
    $ent = new Entrepot($object->db);
    if ($ent->fetch($line->fk_entrepot) > 0) {
        print $ent->getNomUrl(1);
    } else {
        print $langs->trans("Error");
    }
} else {
    print $langs->trans("NA");
}
print '</td>';

// Unit
if (getDolGlobalInt('PRODUCT_USE_UNITS')) {
	print '<td class="linecoluseunit nowrap">';
	$coldisplay++;
	$label = measuringUnitString((int) $line->fk_unit, '', null, 1);
	if ($label !== '') print $langs->trans($label);
	print '</td>';
}

if ($this->status == 0 && $user->hasRight('expedition', 'write') && $action != 'selectlines') {
	print '<td class="linecoledit center">';
	$coldisplay++;
	if (((int) $line->info_bits & 2) != 2 && empty($disableedit)) {
		print '<a class="editfielda reposition" href="'.$_SERVER["PHP_SELF"].'?id='.$this->id.'&action=editline&token='.newToken().'&lineid='.$line->id.'">'.img_edit().'</a>';
	}
	print '</td>';

	print '<td class="linecoldelete center">';
	$coldisplay++;
	print '<a class="reposition" href="'.$_SERVER["PHP_SELF"].'?id='.$this->id.'&action=deleteline&token='.newToken().'&lineid='.$line->id.'">';
	print img_delete();
	print '</a>';
	print '</td>';

	if ($num > 1 && $conf->browser->layout != 'phone' && empty($disablemove)) {
		print '<td class="linecolmove tdlineupdown center">';
		$coldisplay++;
		if ($i > 0) print '<a class="lineupdown" href="'.$_SERVER["PHP_SELF"].'?id='.$this->id.'&action=up&token='.newToken().'&rowid='.$line->id.'">'.img_up('default', 0, 'imgupforline').'</a>';
		if ($i < $num - 1) print '<a class="lineupdown" href="'.$_SERVER["PHP_SELF"].'?id='.$this->id.'&action=down&token='.newToken().'&rowid='.$line->id.'">'.img_down('default', 0, 'imgdownforline').'</a>';
		print '</td>';
	} else {
		print '<td class="linecolmove center"></td>';
		$coldisplay++;
	}
} else {
	print '<td colspan="3"></td>';
	$coldisplay += 3;
}

if ($action == 'selectlines') {
	print '<td class="linecolcheck center">';
	print '<input type="checkbox" class="linecheckbox" name="line_checkbox['.($i + 1).']" value="'.$line->id.'">';
	print '</td>';
}

print '</tr>';
print "<!-- END PHP TEMPLATE expedition/objectline_view.tpl.php modif -->\n";