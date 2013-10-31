<?php
  
  use \StructuredDynamics\osf\php\api\ws\auth\registrar\access\AuthRegistrarAccessQuery;
  
  function deleteGroupAccesses($groupURI, $credentials, $queryExtension = NULL) 
  {    
    if(empty($groupURI))
    {
      $groupsByAppID = listGroups($credentials, $queryExtension);
      
      showGroups($groupsByAppID);
      
      $groupNum = getInput('Which group would you like to delete {underscore}all{/underscore} the datasets access permissions?');
      
      $nb = 1;
      
      foreach($groupsByAppID as $appID => $groups)
      {
        foreach($groups as $group)
        {
          if($groupNum == $nb)
          {
            $groupURI = $group;
            break;            
          }
          else
          {
            $nb++;
          }
        }
      }
    }
    
    $yes = getInput('Are you sure you want to delete dataset accesses of the "'.$groupURI.'" group?', TRUE);
    
    if($yes === NULL)
    {
      $yes = FALSE;
    }      
    
    if($yes)
    {
      $authRegistrarAccess = new AuthRegistrarAccessQuery($credentials['osf-web-services'], $credentials['application-id'], $credentials['api-key'], $credentials['user']);
      
      $authRegistrarAccess->deleteAllGroups($groupURI)
                          ->send($queryExtension);
      
      if($authRegistrarAccess->isSuccessful())
      {
        cecho("\nAll datasets accesses deleted for that group!\n\n", 'GREEN');
        
        exit(0);                
      }
      else
      {
        $debugFile = md5(microtime()).'.error';
        file_put_contents('/tmp/'.$debugFile, var_export($authRegistrarAccess, TRUE));
             
        @cecho('Couldn\'t create the new group.'. $authRegistrarAccess->getStatusMessage() . 
             $authRegistrarAccess->getStatusMessageDescription()."\nDebug file: /tmp/$debugFile\n", 'RED');                
             
        exit(1);                
      }
    }
    else
    {
      exit(2);
    }    
    

    exit(10);
  }
         
?>
