<?php
  
  use \StructuredDynamics\osf\php\api\ws\auth\registrar\group\AuthRegistrarGroupQuery;
  
  function deleteGroup($groupURI, $credentials, $queryExtension = NULL) 
  {
    if(empty($groupURI))
    {
      $groupsByAppID = listGroups($credentials, $queryExtension);
      
      showGroups($groupsByAppID);
      
      $groupNum = getInput('Which group would you like to delete?');
      
      $nb = 1;
      
      foreach($groupsByAppID as $appID => $groups)
      {
        foreach($groups as $group)
        {
          if($groupNum == $nb)
          {
            $yes = getInput('Are you sure you want to delete the "'.$group.'" group?', TRUE);
            
            if($yes === NULL)
            {
              $yes = FALSE;
            }      
            
            if($yes)
            {
              $authRegistrarGroup = new AuthRegistrarGroupQuery($credentials['osf-web-services'], $credentials['application-id'], $credentials['api-key'], $credentials['user']);
              
              $authRegistrarGroup->application($appID)
                                 ->group($group)
                                 ->deleteGroup()
                                 ->send($queryExtension);
                                 
              if($authRegistrarGroup->isSuccessful())
              {
                cecho("\nGroup Deleted!\n\n", 'GREEN');
                
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
            }
            else
            {
              exit(2);
            }
          }
          else
          {
            $nb++;
          }
        }
      }
    }

    exit(10);
  }
         
?>
