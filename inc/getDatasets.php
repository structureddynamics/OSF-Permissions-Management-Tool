<?php

  use \StructuredDynamics\osf\php\api\ws\dataset\read\DatasetReadQuery;

  function getDatasets($credentials)
  {
    $datasetRead = new DatasetReadQuery($credentials['osf-web-services'], $credentials['application-id'], $credentials['api-key'], $credentials['user']);
    
    $datasetRead->uri('all')
                ->send();
    
    if($datasetRead->isSuccessful())
    {
      $resultset = $datasetRead->getResultset()->getResultset();

      $url = parse_url($credentials['osf-web-services']);
      
      $datasets = array(
        'core' => array(
          array(
            'uri' => 'http://'.$url['host'].'/wsf/',
            'label' => 'Core WSF dataset. Used to create permissions and register endpoints',
          ),
          array(
            'uri' => 'http://'.$url['host'].'/wsf/datasets/',
            'label' => 'Datasets dataset. Used to create new datasets',
          ),
          array(
            'uri' => 'http://'.$url['host'].'/wsf/ontologies/',
            'label' => 'Ontologies dataset. Used to create new ontologies',
          ),
        ),
        'normal' => array(),
        'ontology' => array()
      );
      
      if(isset($resultset['unspecified']))
      {
        foreach($resultset['unspecified'] as $uri => $dataset)
        {
          $isOntologyDataset = FALSE;
          
          $dset = array(
            'uri' => '',
            'label' => '',
            'description' => '',
            'created' => '',
            'modified' => ''
          );        

          $dset['uri'] = $uri;
          $dset['label'] = $dataset['prefLabel'];
          
          if(isset($dataset['description']))
          {
            $dset['created'] = $dataset['description'];
          }
          
          if(isset($dataset['http://purl.org/dc/terms/created']))
          {
            $dset['created'] = $dataset['http://purl.org/dc/terms/created'][0]['value'];
          }
          
          if(isset($dataset['http://purl.org/dc/terms/modified']))
          {
            $dset['modified'] = $dataset['http://purl.org/dc/terms/modified'][0]['value'];
          }

          if(isset($dataset['http://purl.org/ontology/wsf#holdOntology']))
          {
            $isOntologyDataset = filter_var($dataset['http://purl.org/ontology/wsf#holdOntology'][0]['value'], FILTER_VALIDATE_BOOLEAN);
          }          
          
          if($isOntologyDataset)
          {
            array_push($datasets['ontology'], $dset);        
          }
          else
          {
            array_push($datasets['normal'], $dset);        
          }
        }
      }
      
      return($datasets);
    }
    else
    {
      $debugFile = md5(microtime()).'.error';
      file_put_contents('/tmp/'.$debugFile, var_export($datasetRead, TRUE));
     
      @cecho('Can\'t get accessible datasets. '. $datasetRead->getStatusMessage() . 
             $datasetRead->getStatusMessageDescription()."\nDebug file: /tmp/$debugFile\n", 'RED');
             
      exit(1);
    }   
  }
  
  function showDatasets($datasets)
  {
    $nb = 0;
    
    foreach($datasets as $type => $dsets)
    {
      switch($type)
      {
        case 'core':
          cecho("Core datasets: \n", 'WHITE');
        break;
        case 'dataset':
          cecho("Datasets: \n", 'WHITE');
        break;
        case 'ontology':
          cecho("Ontologies: \n", 'WHITE');
        break;
      }
      
      foreach($dsets as $dataset)
      {
        $nb++;
        
        cecho("  ($nb) ".$dataset['label']." ({cyan}".$dataset['uri']."{/cyan})\n", 'WHITE');
      }
      
      echo "\n";
    }

    cecho("\n", 'WHITE');
  }
  
?>
