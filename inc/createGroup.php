<?php
  
  use \StructuredDynamics\osf\php\api\ws\auth\registrar\group\AuthRegistrarGroupQuery;
  
  function createGroup($groupURI, $appID, $credentials, $queryExtension = NULL) 
  {
    if(empty($groupURI))
    {
      $groupURI = getInput("What is the URI of the group you want to create?");  
    }
    
    if(empty($appID))
    {
      $applicationID = getInput("What is the Application ID where you want to create that group?");
    }
    
    $authRegistrarGroup = new AuthRegistrarGroupQuery($credentials['osf-web-services'], $credentials['application-id'], $credentials['api-key'], $credentials['user']);

    $authRegistrarGroup->group($groupURI)
                       ->application($applicationID)
                       ->createGroup()
                       ->mime('resultset')
                       ->send($queryExtension);
               
    if($authRegistrarGroup->isSuccessful())
    {
      cecho("\nGroup Created!\n\n", 'GREEN');
      exit(0);
    }
    else
    {
      $debugFile = md5(microtime()).'.error';
      file_put_contents('/tmp/'.$debugFile, var_export($authRegistrarGroup, TRUE));
           
      @cecho('Couldn\'t create the new group.'. $authRegistrarGroup->getStatusMessage() . 
           $authRegistrarGroup->getStatusMessageDescription()."\nDebug file: /tmp/$debugFile\n", 'RED');
      exit(1);
    }
    
    exit(10);
  }
?>
