<?php // content="text/plain; charset=utf-8"
	
 require_once ('jpgraph/jpgraph.php');
 require_once ('jpgraph/jpgraph_bar.php');
 require_once ('jpgraph/jpgraph_line.php');
 
 // 数据
 $data_follow = array(140,110,77,104,29,161,13,195);
 
 // 构造对象
 $graph = new Graph(320,440);   //屏幕分辨率
 
 // 基本参数
 $graph->SetScale("textlin");    //线性标尺
 $graph->SetY2Scale('lin',0,100);   //对数
 $graph->Set90AndMargin(50,0,65,0); //旋转90度
 $graph->yaxis->SetTitleMargin(25);
 
 // 标题与字体
 $graph->title->Set("Scene Analysis");
 $graph->title->SetFont(FF_FONT1,FS_BOLD);
 $graph->xaxis->title->Set("Sce");
 $graph->yaxis->title->Set("Num");
 $graph->y2axis->SetColor('black','blue');
 $graph->y2axis->SetLabelFormat('%2d');
 
 // 生成柱状图
 $bplot = new BarPlot($data_follow);
 $bplot->SetFillColor("orange@0.2");
 $bplot->SetValuePos('center');
 $bplot->value->SetFormat("%d");
 $bplot->value->SetFont(FF_ARIAL,FS_NORMAL,9);
 $bplot->value->Show();
 
 // 柱状图叠到图形中
 $graph->Add($bplot);
 
 // 生成图形
 return $graph->Stroke();

?>
