<?php

	// Preview template
		
		$body = "<h2 class=\"weblogdateheader\">".gmdate("F jS, Y",time())."</h2>";
		
		$weblogbody = "The circular arrangement of the axes of the Difference Engine round the large central wheels led to the most extended prospects. The whole of arithmetic now appeared within the grasp of mechanism. A vague glimpse even of an Analytical Engine at length opened out, and I pursued with enthusiasm the shadowy vision. The drawings and the experiments were of the most costly kind. Draftsmen of the highest order were necessary to economize the labour of my own head; whilst skilled workmen were required to execute the experimental machinery to which I was obliged constantly to have recourse.

In order to carry out my pursuits successfully, I had purchased a house with above a quarter of an acre of ground in a very quiet locality. My coach-house was now converted into a forge and a foundry, whilst my stables were transformed into a workshop. I built other extensive workshops myself, and had a fireproof building for my drawings and draftsmen. Having myself worked with a variety of tools, and having studied the art of constructing each of them, I at length laid it down as a principle - that, except in rare cases, I would never do anything myself if I could afford to hire another person who could do it for me.";

		$commentsbody = run("templates:draw", array(
											'context' => 'weblogcomment',
											'postedname' => "Unkind Guest",
											'body' => "I mock you, Babbage! Your difference engine will never catch on.",
											'posted' => gmdate("l, F jS, Y",time()) . " at " . gmdate("H:i",time())
										)
										);

		$commentsbody .= run("templates:draw", array(
											'context' => 'weblogcomment',
											'postedname' => "Charles Babbage",
											'body' => "Says you!",
											'posted' => gmdate("l, F jS, Y",time()) . " at " . gmdate("H:i",time())
										)
										);
										
		$commentsbody = run("templates:draw", array(
										'context' => 'weblogcomments',
										'comments' => $commentsbody
									)
									);
										
		$body .= run("templates:draw", array(
									'context' => 'weblogpost',
									'date' => gmdate("H:i",time()),
									'username' => "charlesbabbage",
									'usericon' => "../../_templates/babbage.jpg",
									'body' => nl2br($weblogbody),
									'fullname' => "Charles Babbage",
									'title' => "The Analytical Engine - introduction from Chapter VIII",
									'comments' => $commentsbody
								)
								);

		$run_result .= run("templates:draw", array(
														'context' => 'infobox',
														'name' => 'Weblog post',
														'contents' => $body
												)
												);
								
?>