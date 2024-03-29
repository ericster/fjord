<?php

// module/Album/src/Album/Controller/AlbumController.php:
namespace Album\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Album\Model\Album;          // <-- Add this import
use Album\Form\AlbumForm;       // <-- Add this import
use Album\Form\UploadForm;       // <-- Add this import
use Album\Form\ExlprepForm;       // <-- Add this import
use Album\Form\ExlPrepValidator;       // <-- Add this import

class AlbumController extends AbstractActionController
{
protected $albumTable;
public function getAlbumTable()
    {
        if (!$this->albumTable) {
            $sm = $this->getServiceLocator();
            $this->albumTable = $sm->get('Album\Model\AlbumTable');
        }
        return $this->albumTable;
    }
	
    public function indexAction()
    {
	 return new ViewModel(array(
            'albums' => $this->getAlbumTable()->fetchAll(),
        ));
    }

    public function uploadAction()
    {
    	$form = new UploadForm('upload-form');
    
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		// Make certain to merge the files info!
    		$post = array_merge_recursive(
    				$request->getPost()->toArray(),
    				$request->getFiles()->toArray()
    		);
    
    		$form->setData($post);
    		if ($form->isValid()) {
    			$data = $form->getData();
    			// Form is valid, save the form!
//     			return $this->redirect()->toRoute('upload-form/success');
    			return $this->redirect()->toRoute('album');
    		}
    	}
    
    	return array('form' => $form);

// 		return new ViewModel(array(
// 	            'albums' => $this->getAlbumTable()->fetchAll(),
// 	        ));

    }

    public function exlprepAction()
    {
    	$form = new ExlprepForm('exlprep');
//     	$form = new UploadForm('upload-form');
    
    	$request = $this->getRequest();
    	if ($request->isPost()) {
    		// Validator
//     		$formValidator = new UploadForm();
    	    $formValidator = new ExlPrepValidator();
    		$form->setInputFilter($formValidator->getInputFilter());
//     		var_dump($form);
//     		$validator = new \Zend\Validator\File\Extension(array('php', 'exe'));

    		// Make certain to merge the files info!
    		$post = array_merge_recursive(
    				$request->getPost()->toArray(),
    				$request->getFiles()->toArray()
    		);
    		$form->setData($post);
    		
    		// Fine moved to new location: considered to be having this option in the filter
    		// BUG: how to handle an array?
    		$files   = $request->getFiles()->toArray();
//     		$file = $fileArr['uploadExl'];
    		print_r($files);
//     		$filter = new \Zend\Filter\File\RenameUpload("/Applications/MAMP/htdocs/myapp/public/data/uploads/");
//     		$filter->setUseUploadName(true);
//     		echo $filter->filter($files['uploadExl']);
//     		print_r($files);

//     		var_dump($form);
    		if ($form->isValid()) {
    			$data = $form->getData();
    			// Form is valid, save the form!
    			print_r("success!\n");
    			print_r($data);
//     			return $this->redirect()->toRoute('album');

    			// Regex pattern from form to write
				$file_loc = '/Applications/MAMP/htdocs/myapp/public/data/appRegex/';
    			$myFile = $file_loc . "testFile.txt";
    			$fh = fopen($myFile, 'w') or die("can't open file");
    			$stringData = "Floppy Jalopy\n";
    			fwrite($fh, $stringData);
    			$stringData = "Pointy Pinto\n";
    			fwrite($fh, $stringData);
    			
    			$searchTerms = $data['searchTerm'];
				print_r($searchTerms);
    			foreach( $searchTerms as $app ) {
    				$stringData = $app[appName] . ": " . $app[regexPattern] . "\n";
    				print_r($stringData);
	    			fwrite($fh, $stringData);
    			}
    			fclose($fh);
    		}
    		else {
    			print_r($form->getMessages()); //stringLengthTooLong error will show
    			$data = $form->getData();
    			print_r("fail!\n");
    			print_r($data);
//     			return $this->redirect()->toRoute('album');
//     			var_dump($form->getMessages()); //stringLengthTooLong error will show
//     			print_r($form->getErrors()); //stringLengthTooLong error will show
//     			return $this->redirect()->toRoute('album', array(
//     					'controller' => 'Album\Controller\Album',
//     					'action'     => 'python',
// 				));
    		}
    	}
    
//     	$viewModel = new ViewModel();
//     	$viewModel->setTerminal(true);
//     	return new ViewModel();
    	return array('form' => $form);

// 		return new ViewModel(array(
// 	            'albums' => $this->getAlbumTable()->fetchAll(),
// 	        ));
    }
    
    public function downloadAction()
    {
    	/* get here all the data you need from the database
    	 * $size = size of the file you can get by readfile()
    	* $filename = 12f3f1aa1b11ec11dd1dd1.zip (with the path)
    	* $filename1 = example.zip
    	*/
    
    	if(file_exists($filename)) {
    	ob_start();
    	$size = readfile($filename);
    	$this->view->data = ob_get_clean();
    }
    
      header('Expires: Mon, 20 May 1974 23:58:00 GMT');
	  header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
	  header('Cache-Control: no-store, no-cache, must-revalidate');
	  header('Cache-Control: post-check=0, pre-check=0', false);
	  header('Cache-Control: private');
      header('Pragma: no-cache');
      header("Content-Transfer-Encoding: binary");
	  header("Content-type: binary/octet-stream");
	  header("Content-length: {$size}");
      header("Content-disposition: attachment; filename=\"{$filename1}\"");
    
      $this->_helper->layout()->disableLayout();
      $this->render('download');
    }
    
//     make a view file (download.phtml) with this:
//     <?php echo $this->data; 

    public function pythonAction()
    {
	 return new ViewModel(array(
            'albums' => $this->getAlbumTable()->fetchAll(),
        ));
	//$appProc = 'python /Users/Eric/Documents/workspace/TCforTestLink/TestLink/appPattern.py';
	//exec($appProc, $output, $return);
	//system($appProc, $return);
	//echo "Dir returned $return, and output:\n";
	//Zend_Debug::dump($output, $label = null, $echo = true);
	//echo "$return \n";
	//echo "============ var_dump ===================\n";
	//var_dump($output);
    }

    // Add content to this method:
    public function addAction()
    {
        $form = new AlbumForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $album = new Album();
            $form->setInputFilter($album->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $album->exchangeArray($form->getData());
                $this->getAlbumTable()->saveAlbum($album);

                // Redirect to list of albums
                return $this->redirect()->toRoute('album');
            }
        }
        return array('form' => $form);
    }	
	
	
// Add content to this method:
    public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('album', array(
                'action' => 'add'
            ));
        }
        $album = $this->getAlbumTable()->getAlbum($id);

        $form  = new AlbumForm();
        $form->bind($album);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setInputFilter($album->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getAlbumTable()->saveAlbum($form->getData());

                // Redirect to list of albums
                return $this->redirect()->toRoute('album');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }	


    // Add content to the following method:
    public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('album');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->getAlbumTable()->deleteAlbum($id);
            }

            // Redirect to list of albums
            return $this->redirect()->toRoute('album');
        }

        return array(
            'id'    => $id,
            'album' => $this->getAlbumTable()->getAlbum($id)
        );
    }
	
	
	
	
}

