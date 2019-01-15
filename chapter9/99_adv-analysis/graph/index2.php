<?php // content="text/plain; charset=utf-8"
	
	require_once ('jpgraph/jpgraph.php');
	require_once ('jpgraph/jpgraph_bar.php');
	require_once ('jpgraph/jpgraph_line.php');
	
	
	// some data
	$data_weight = array(120,130,123,100,150,160,170,200,220,240);
	$data_fat = array(22,20,12,16,15,24,37,39,40,44);

	// Create the graph. 
	$graph = new Graph(250,400);
	// $graph->SetAngle(90);

	// Setup some basic graph parameters
	$graph->SetScale("textlin");
	$graph->SetY2Scale('lin',0,100);
	// $graph->img->SetMargin(50,70,30,40);
	$graph->Set90AndMargin(40,40,60,40);
	$graph->yaxis->SetTitleMargin(30);
	$graph->SetMarginColor('#EEEEEE');

	// $title = iconv("UTF-8", "gb2312", $title);

	// Setup titles and fonts
	$graph->title->Set("Health Monitor");
	$graph->xaxis->title->Set("T");
	$graph->yaxis->title->Set("W");

	$graph->title->SetFont(FF_FONT1,FS_BOLD);


	// Turn the tickmarks
	$graph->xaxis->SetTickSide(SIDE_DOWN);
	$graph->yaxis->SetTickSide(SIDE_LEFT);

	$graph->y2axis->SetTickSide(SIDE_RIGHT);
	$graph->y2axis->SetColor('black','blue');
	$graph->y2axis->SetLabelFormat('%2d.0');

	// Create a bar pot
	$bplot = new BarPlot($data_weight);

	// Create accumulative graph
	$lplot = new LinePlot($data_fat);

	// We want the line plot data point in the middle of the bars
	$lplot->SetBarCenter();

	// Use transperancy
	$lplot->SetFillColor('lightblue@0.6');
	$lplot->SetColor('blue@0.6');
	$graph->AddY2($lplot);

	// Setup the bars
	$bplot->SetFillColor("orange@0.2");
	$bplot->SetValuePos('center');
	$bplot->value->SetFormat("%d");
	$bplot->value->SetFont(FF_ARIAL,FS_NORMAL,9);
	$bplot->value->Show();

	// Add it to the graph
	$graph->Add($bplot);

	// Send back the HTML page which will call this script again
	// to retrieve the image.
	return $graph->Stroke();
?>
