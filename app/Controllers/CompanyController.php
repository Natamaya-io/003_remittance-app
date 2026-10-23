<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Auth;
use App\Core\BaseController;
use App\Services\AuditService;
use PDO;

final class CompanyController extends BaseController
{
    public function index(): void
    {
        $this->permission('company.view');
        $q = trim((string) ($_GET['q'] ?? ''));
        $sql = 'SELECT c.*, u.name creator_name FROM companies c LEFT JOIN users u ON u.id=c.created_by WHERE 1=1'; $params=[];
        if ($q !== '') { $sql .= ' AND (c.name LIKE ? OR c.code LIKE ? OR c.email LIKE ?)'; $params=array_fill(0,3,"%{$q}%"); }
        $sql .= ' ORDER BY c.name';
        $stmt=App::db()->prepare($sql); $stmt->execute($params); $companies=$stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->view('companies/index', compact('companies','q')+['title'=>'Data PT / Perusahaan']);
    }
    public function create(): void { $this->permission('company.manage'); $this->view('companies/form',['title'=>'Tambah Perusahaan','company'=>null]); }
    public function store(): void
    {
        $this->permission('company.manage');
        $data=$this->validated();
        $stmt=App::db()->prepare('INSERT INTO companies (code,name,tax_id,address,contact_name,contact_phone,email,status,created_by,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,NOW(),NOW())');
        $stmt->execute([$data['code'],$data['name'],$data['tax_id'],$data['address'],$data['contact_name'],$data['contact_phone'],$data['email'],$data['status'],Auth::id()]);
        $id=(int)App::db()->lastInsertId(); AuditService::log('create','company',$id,null,$data); flash('success','Perusahaan berhasil ditambahkan.'); redirect('/companies');
    }
    public function edit(string $id): void
    {
        $this->permission('company.manage'); $company=$this->find((int)$id); $this->view('companies/form',['title'=>'Ubah Perusahaan','company'=>$company]);
    }
    public function update(string $id): void
    {
        $this->permission('company.manage'); $before=$this->find((int)$id); $data=$this->validated();
        $stmt=App::db()->prepare('UPDATE companies SET code=?,name=?,tax_id=?,address=?,contact_name=?,contact_phone=?,email=?,status=?,updated_at=NOW() WHERE id=?');
        $stmt->execute([$data['code'],$data['name'],$data['tax_id'],$data['address'],$data['contact_name'],$data['contact_phone'],$data['email'],$data['status'],(int)$id]);
        AuditService::log('update','company',(int)$id,$before,$data); flash('success','Perusahaan berhasil diperbarui.'); redirect('/companies');
    }
    public function destroy(string $id): void
    {
        $this->permission('company.manage'); $before=$this->find((int)$id);
        $countStmt=App::db()->prepare('SELECT COUNT(*) FROM transactions WHERE payer_company_id=? AND deleted_at IS NULL'); $countStmt->execute([(int)$id]);
        if ((int)$countStmt->fetchColumn()>0) { flash('danger','Perusahaan tidak dapat dihapus karena memiliki transaksi. Nonaktifkan statusnya.'); redirect('/companies'); }
        App::db()->prepare('DELETE FROM companies WHERE id=?')->execute([(int)$id]); AuditService::log('delete','company',(int)$id,$before,null); flash('success','Perusahaan dihapus.'); redirect('/companies');
    }
    private function validated(): array
    {
        $name=trim((string)($_POST['name']??'')); $code=strtoupper(trim((string)($_POST['code']??'')));
        if($name===''||$code===''){ remember_old($_POST); flash('danger','Kode dan nama perusahaan wajib diisi.'); redirect($_SERVER['HTTP_REFERER']??'/companies'); }
        clear_old();
        return ['code'=>$code,'name'=>$name,'tax_id'=>trim((string)($_POST['tax_id']??''))?:null,'address'=>trim((string)($_POST['address']??''))?:null,'contact_name'=>trim((string)($_POST['contact_name']??''))?:null,'contact_phone'=>trim((string)($_POST['contact_phone']??''))?:null,'email'=>trim((string)($_POST['email']??''))?:null,'status'=>in_array($_POST['status']??'active',['active','inactive'],true)?$_POST['status']:'active'];
    }
    private function find(int $id): array { $stmt=App::db()->prepare('SELECT * FROM companies WHERE id=?');$stmt->execute([$id]);$row=$stmt->fetch(PDO::FETCH_ASSOC);if(!$row){http_response_code(404);throw new \RuntimeException('Perusahaan tidak ditemukan.');}return $row; }
}
