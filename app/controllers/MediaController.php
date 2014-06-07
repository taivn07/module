<?php
use Phalcon\Paginator\Adapter\Model as Paginator;

class MediaController extends ControllerBase {

	public function uploadAction() {
        $media = $this->request->getJsonRawBody();

		if ($this->request->hasFiles() == true) {
			$upload = $this->request->getUploadedFiles()[0];

            // get extension of file
            $extension = pathinfo($upload->getname(), PATHINFO_EXTENSION);

            // move tmp file to file path
            $fileName = sprintf('%s-%s', $this->common->generateRandomString(5), date('YmdHis'));

            $type = $this->request->getPost()['type'];
            $size = $upload->getsize();
            $path = 'temp/'.$fileName.'.'.$extension;
            $upload->moveTo($path);

            // create thumbnail and get information
            $uploader = $this->uploader;
            if ($type == "image") {
                // get thumbnail small image
                $thumbnailPath  =  dirname($path) . '/' .sprintf('%s-thumbnail', $fileName).'.'.$extension;
                $uploader->ResizeToDimension(300, $path, $extension, $thumbnailPath);
                $thumbnailSize = $uploader->get_image_size($thumbnailPath);
            }

            if ($type == "video") {
                // get thumnail small image
                $video_thumbnailPath = $uploader->videoThumnail($path, $fileName);
                $thumbnailPath  =  dirname($video_thumbnailPath) . '/' .sprintf('%s-thumbnail', $fileName).'.jpg';
                $uploader->ResizeToDimension(300, $video_thumbnailPath, 'jpg', $thumbnailPath);
                $thumbnailSize = $uploader->get_image_size($thumbnailPath);

                if (filesize($path)/1024 > 5*1024) {
                    $newVideo = $uploader->resize_video($path, $fileName, $extension);
                    $new_path = $newVideo;
                    $size = filesize($newVideo);

                    unlink($path);
                } else {
                    
                    
                }
            }

            $phql = "INSERT INTO Medias (extension, size, originPath, thumbnailPath, thumbnailSize)
                    VALUES(:extension:, :size:, :originPath:, :thumbnailPath:, :thumbnailSize:)";
            $status = $this->modelsManager->executeQuery($phql, array(
                'extension' => $extension,
                'size' => $size,
                'originPath' => $path,
                'thumbnailPath' => $thumbnailPath,
                'thumbnailSize' => $thumbnailSize,
            ));

            // set response to json
            $this->setJsonResponse();

            // check if inserton was successful
            if ($status->success() == true) {
                // change the HTTP status
                $this->response->setStatusCode(201, "Created");

                $media = $status->getModel();
                // $media->id = $status->getModel()->id;
                $media->fullPath = $media->getFullPath();
                $media->fullThumbnailPath = $media->getFullThumbnailPath();

                return array('status' => 'OK', 'data' => $media);

            } else {
                $this->response->setStatusCode(409, "Conflict");

                // send errors to client
                $errors = array();
                foreach ($status->getMessages() as $message) {
                    $errors[] = $message->getMessage();
                }

                return array('status' => 'ERROR', 'message' => $errors);
            }
		}
	}

    public function indexAction() {
        // start from 1
        $currentPage = 1;

        $phql = "SELECT * FROM Medias";
        $result = $this->modelsManager->executeQuery($phql);

        $page = new Paginator(array(
            'data' => $result,
            'limit' => 5,
            'page' => $currentPage
        ));
        $medias = $page->getPaginate();

        // set response to json
        $this->setJsonResponse();

        $data = array();
        foreach ($medias->items as $media) {
            $data[] = array(
                'id' => $media->id,
                'originPath' => BASE_URL.$media->originPath,
                'thumbnailPath' => BASE_URL.$media->thumbnailPath,
                'thumbnailSize' => $media->thumbnailSize,
            );
        }

        return array('status' => 'OK', 'data' => $data);
    }
}