<?php
  
  use \StructuredDynamics\osf\php\api\ws\auth\registrar\user\AuthRegistrarUserQuery;
  
  function unregisterUser($userURI, $groupURI, $credentials, $queryExtension = NULL) 
  {
    if(empty($userURI))
    {
      $groupsByAppID = listGroups($credentials, $queryExtension);
      
      showGroups($groupsByAppID);
      
      $groupNum = getInput('What is the group where you would like to unregister a user?');
      
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
        
        if(!empty($groupURI))
        {
          break;
        }
      }
      
      $groupUsers = listGroupUsers($groupURI, $credentials, $queryExtension);

      showGroupUsers($groupUsers);
      
      $userNum = getInput('What is the user you would like to unregister from that group?');
      
      foreach($groupUsers[$groupURI] as $key => $user)
      {
        if($userNum == ($key + 1))
        {
          $userURI = $user;
          break;
        }
      }                  
    }
    elseif(empty($groupURI))
    {
      $groupsByAppID = listGroups($credentials, $queryExtension);
      
      showGroups($groupsByAppID);
      
      $groupNum = getInput('What is the group where you would like to unregister the user?');
      
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
        
        if(!empty($groupURI))
        {
          break;
        }
      }
    }    
    
    $authRegistrarUser = new AuthRegistrarUserQuery($credentials['osf-web-services'], $credentials['application-id'], $credentials['api-key'], $credentials['user']);
    
    $authRegistrarUser->user($userURI)
                      ->group($groupURI)
                      ->leaveGroup()
                      ->send($queryExtension);
    
               
    if($authRegistrarUser->isSuccessful())
    {
      cecho("\nUser Unregistered!\n\n", 'GREEN');
      
      exit(0);
    }
    else
    {
      $debugFile = md5(microtime()).'.error';
      file_put_contents('/tmp/'.$debugFile, var_export($authRegistrarUser, TRUE));
           
      @cecho('Can\'t unregister the user: '.$groupURI.' '. $authRegistrarUser->getStatusMessage() . 
           $authRegistrarUser->getStatusMessageDescription()."\nDebug file: /tmp/$debugFile\n", 'RED');
           
      exit(1);
    }
    
    exit(10);   
  }
         
?>
