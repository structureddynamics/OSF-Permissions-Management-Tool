<?php
  
  use \StructuredDynamics\osf\php\api\ws\auth\registrar\user\AuthRegistrarUserQuery;
  
  function registerUser($userURI, $groupURI, $credentials, $queryExtension = NULL) 
  {
    if(empty($userURI))
    {
      $userURI = getInput("What is the URI of the user you want to register?");
    }
    
    if(empty($groupURI))
    {
      $groupsByAppID = listGroups($credentials, $queryExtension);
      
      showGroups($groupsByAppID);
      
      $groupNum = getInput('What is the group where you would like to register the user?');
      
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
                      ->joinGroup()
                      ->send($queryExtension);
    
               
    if($authRegistrarUser->isSuccessful())
    {
      cecho("\nUser Registered!\n\n", 'GREEN');
      
      exit(0);
    }
    else
    {
      $debugFile = md5(microtime()).'.error';
      file_put_contents('/tmp/'.$debugFile, var_export($authRegistrarUser, TRUE));
           
      @cecho('Can\'t list the users of that group: '.$groupURI.' '. $authRegistrarUser->getStatusMessage() . 
           $authRegistrarUser->getStatusMessageDescription()."\nDebug file: /tmp/$debugFile\n", 'RED');
           
      exit(1);
    }
    
    exit(10);
  }
         
?>
