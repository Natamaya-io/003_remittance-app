<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\BaseController;
use PDO;

final class AuditController extends BaseController
{
    public function index():void{$this->permission('audit.view');$q=trim((string)($_GET['q']??''));$sql='SELECT a.*,u.name user_name FROM audit_logs a LEFT JOIN users u ON u.id=a.user_id WHERE 1=1';$p=[];if($q!==''){$sql.=' AND (a.action LIKE ? OR a.entity_type LIKE ? OR u.name LIKE ?)';$p=array_fill(0,3,'%'.$q.'%');}$sql.=' ORDER BY a.created_at DESC LIMIT 500';$s=App::db()->prepare($sql);$s->execute($p);$logs=$s->fetchAll(PDO::FETCH_ASSOC);$this->view('audit/index',compact('logs','q')+['title'=>'Audit Log']);}
}
