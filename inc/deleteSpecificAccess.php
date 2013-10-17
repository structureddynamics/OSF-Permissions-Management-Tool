<?php
  
  use \StructuredDynamics\osf\php\api\ws\auth\registrar\access\AuthRegistrarAccessQuery;
  
  function deleteSpecificAccess($accessURI, $credentials, $queryExtension = NULL) 
  {
    if(empty($accessURI))
    {
      $groups = listGroups($credentials, $queryExtension);
      
      if(empty($groups))
      {
        cecho("\nThere are currently no group in the OSF instance\n\n", 'YELLOW');
        
        exit(1);
      }      
      
      showUserGroups($groups);  
      
      $groupNum = getInput('Which group accesses would you like to expend?');
      
      $nb = 1;
      foreach($groups as $appID => $grps)
      {
        foreach($grps as $group)
        {
          if($groupNum == $nb)
          {
            $groupURI = $group;
          }
          
          $nb++;
        }
      }
      
      $permissions = listGroupPermissions($groupURI, $credentials, $queryExtension);
      
      showGroupPermissions($permissions);
      
      $accessNum = getInput('Which access permissions would you like to delete?');
    
      $nb = 1;
      foreach(array_keys($permissions[$groupURI]) as $key => $access)
      {
        if($accessNum == ($key + 1))
        {
          $accessURI = $access;
          
          break;
        }
      }      
    }
    
    $authRegistrarAccess = new AuthRegistrarAccessQuery($credentials['osf-web-services'], $credentials['application-id'], $credentials['api-key'], $credentials['user']);
    
    $authRegistrarAccess->deleteSpecific($accessURI)
                        ->send($queryExtension);
                        
    if($authRegistrarAccess->isSuccessful())
    {
      cecho("\nAccess permission delete!\n\n", 'green');
      
      exit(0);
    }
    else
    {
      $debugFile = md5(microtime()).'.error';
      file_put_contents('/tmp/'.$debugFile, var_export($authRegistrarAccess, TRUE));
           
      @cecho('Couldn\'t delete the access permission'. $authRegistrarAccess->getStatusMessage() . 
           $authRegistrarAccess->getStatusMessageDescription()."\nDebug file: /tmp/$debugFile\n", 'RED');
      
      exit(1);
    }        
    
    exit(10);
  }
?>
