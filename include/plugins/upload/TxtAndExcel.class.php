<?php
 /* 
  excel及txt处理类
*/
 class TxtAndExcel extends Plugin
 {
      /**
     * 读取上传文件，返回数组
     */
    public function ImportPhone() {
        static $aliases = array(
            'xlsx' => 'xls',
        );

        if(!empty($_POST)) {
            if(empty($_FILES['source']) || $_FILES['source']['error'] == UPLOAD_ERR_NO_FILE) {
                $this->error('请选择要导入的文件');
            }
            $source = $_FILES['source'];
            $ext = strtolower(substr($source['name'], strrpos($source['name'], '.') + 1));
            $ext = isset($aliases[$ext]) ? $aliases[$ext] : $ext;
            $solver = '_importBy' . ucfirst($ext);
            if(method_exists($this, $solver)) {
                $customers = $this->$solver($source);
            } else {
                $customers=array();
            }		
			return $customers;
        }else
		{
			return false;
		}

	
    }
	
   /**
     * 文本格式导入
     *
     * @param array $source
     * @return array
     */
    protected function _importByTxt($source) {
        $customers = array();
        $createTime = time();
        $content = file_get_contents($source['tmp_name']);
        $items = explode("\n", $content);
        foreach($items as $item) {
            list($name, $phone) = explode(',', $item);
            if(!empty($name) && !empty($phone)) {
                $customers[] = array($name, $phone);
            }
        }

        return $customers;
    }

    /**
     * CVS格式导入
     *
     * @param array $source
     * @return array
     */
    protected function _importByCvs($source) {
        return $this->_importByTxt($source);
    }

    /**
     * Excel格式导入
     *
     * @param array $source
     * @return array
     */
    protected function _importByXls($source) {
        $customer = $customers = array();
        include PLUGIN_PATH . SEP . 'excel' . SEP . 'PHPExcel' . SEP . 'IOFactory.php';
        $excel = PHPExcel_IOFactory::load($source['tmp_name']);
        $sheet = $excel->getActiveSheet()->toArray(null, true, true, true);

        foreach($sheet as $rows) {
            foreach($rows as $cols) {
                $customer[] = $cols;
            }
            $customers[] = $customer;
        }

        return $customers;
    }
 }
