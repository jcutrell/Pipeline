<?phprequire_once('./../../global.php');$slug = Filter::text($_GET['slug']);	// check project$project = Project::getProjectFromSlug($slug);if($project == null) {	Session::setMessage('That project does not exist.');	header('Location: '.Url::error());	exit();}$action = Filter::text($_POST['action']);$userID = Filter::numeric($_POST['userID']);$user = User::load($userID);	if($action == 'revoke-organizer') {	ProjectUser::delete($project->getID(), $userID);		// was this organizer leading any tasks?	$tasks = Task::getByLeaderID($project->getID(), $userID);	if($tasks != null) {		foreach($tasks as $t) {			// revert task leader to project creator			$oldLeaderID = $t->getLeaderID();			$newLeaderID = $project->getCreatorID();			if($oldLeaderID != $newLeaderID) {				// save it				$t->setLeaderID($newLeaderID);				$t->save();				// log it				$logEvent = new Event(array(					'event_type_id' => 'edit_task_leader',					'project_id' => $project->getID(),					'user_1_id' => Session::getUserID(),					'user_2_id' => $newLeaderID,					'item_1_id' => $t->getID(),					'data_1' => $oldLeaderID,					'data_2' => $newLeaderID				));				$logEvent->save();			}		}	}		// send us back	Session::setMessage($user->getUsername().' is no longer an organizer.');	$json = array('success' => '1');	echo json_encode($json);} elseif($action == 'make-organizer') {	if(ProjectUser::isFollower($userID, $project->getID())) {		ProjectUser::changeRelationship($project->getID(), $userID, ProjectUser::ORGANIZER);	} else {		$pu = new ProjectUser(array(			'user_id' => $userID,			'project_id' => $project->getID(),			'relationship' => ProjectUser::ORGANIZER		));		$pu->save();	}		// send us back	Session::setMessage($user->getUsername().' is now an organizer.');	$json = array('success' => '1');	echo json_encode($json);} elseif($action == 'ban') {	if(ProjectUser::isFollower($userID, $project->getID())) {		ProjectUser::changeRelationship($project->getID(), $userID, ProjectUser::BANNED);	} else {		$pu = new ProjectUser(array(			'user_id' => $userID,			'project_id' => $project->getID(),			'relationship' => ProjectUser::BANNED		));		$pu->save();	}		// send us back	Session::setMessage($user->getUsername().' is now banned.');	$json = array('success' => '1');	echo json_encode($json);} elseif($action == 'unban') {	ProjectUser::delete($project->getID(), $userID);	} else {	$json = array('error' => 'Invalid action.');	exit(json_encode($json));	}