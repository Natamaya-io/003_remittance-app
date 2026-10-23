<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Auth;
use App\Core\BaseController;
use App\Services\AuditService;
use PDO;

final class PayerAccountController extends BaseController
{
    public function index(): void
    {
        $this->permission('payer_account.view');

        $filters = [
            'q' => trim((string) ($_GET['q'] ?? '')),
            'company_id' => (int) ($_GET['company_id'] ?? 0),
            'status' => trim((string) ($_GET['status'] ?? 'active')),
        ];

        $where = ' WHERE 1=1';
        $params = [];
        if ($filters['q'] !== '') {
            $where .= ' AND (pa.account_no LIKE ? OR pa.account_name LIKE ? OR pa.bank_name LIKE ? OR pa.swift LIKE ? OR c.name LIKE ?)';
            $needle = '%' . $filters['q'] . '%';
            array_push($params, $needle, $needle, $needle, $needle, $needle);
        }
        if ($filters['company_id'] > 0) {
            $where .= ' AND pa.company_id=?';
            $params[] = $filters['company_id'];
        }
        if (in_array($filters['status'], ['active', 'inactive'], true)) {
            $where .= ' AND pa.status=?';
            $params[] = $filters['status'];
        }

        $statement = App::db()->prepare(
            "SELECT pa.*, c.name company_name, u.name creator_name
             FROM payer_accounts pa
             JOIN companies c ON c.id=pa.company_id
             LEFT JOIN users u ON u.id=pa.created_by
             {$where}
             ORDER BY pa.is_default DESC,c.name,pa.bank_name,pa.account_no"
        );
        $statement->execute($params);

        $companies = App::db()->query("SELECT id,name FROM companies WHERE status='active' ORDER BY name")
            ->fetchAll(PDO::FETCH_ASSOC);

        $this->view('payer_accounts/index', [
            'title' => 'Master Rekening Payer',
            'accounts' => $statement->fetchAll(PDO::FETCH_ASSOC),
            'companies' => $companies,
            'filters' => $filters,
        ]);
    }

    public function create(): void
    {
        $this->permission('payer_account.manage');
        $companies = App::db()->query("SELECT id,name FROM companies WHERE status='active' ORDER BY name")
            ->fetchAll(PDO::FETCH_ASSOC);
        $this->view('payer_accounts/form', [
            'title' => 'Tambah Rekening Payer',
            'account' => null,
            'companies' => $companies,
        ]);
    }

    public function store(): void
    {
        $this->permission('payer_account.manage');
        $data = $this->validated();

        $db = App::db();
        $db->beginTransaction();
        try {
            if ((int) $data['is_default'] === 1) {
                $db->prepare('UPDATE payer_accounts SET is_default=0,updated_at=NOW() WHERE company_id=?')
                    ->execute([(int) $data['company_id']]);
            }

            $statement = $db->prepare(
                'INSERT INTO payer_accounts
                (company_id,account_no,account_name,bank_name,bank_branch,swift,currency,email,address,city,country,is_default,status,created_by,created_at,updated_at)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())'
            );
            $statement->execute([
                $data['company_id'], $data['account_no'], $data['account_name'], $data['bank_name'],
                $data['bank_branch'], $data['swift'], $data['currency'], $data['email'],
                $data['address'], $data['city'], $data['country'], $data['is_default'],
                $data['status'], Auth::id(),
            ]);
            $id = (int) $db->lastInsertId();
            $db->commit();

            AuditService::log('create', 'payer_account', $id, null, $data);
            flash('success', 'Rekening payer berhasil ditambahkan dan siap dipilih pada transaksi manual.');
            redirect('/payer-accounts');
        } catch (\Throwable $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            remember_old($_POST);
            flash('danger', 'Rekening gagal disimpan: ' . $exception->getMessage());
            redirect('/payer-accounts/create');
        }
    }

    public function edit(string $id): void
    {
        $this->permission('payer_account.manage');
        $account = $this->find((int) $id);
        $companies = App::db()->query("SELECT id,name FROM companies WHERE status='active' ORDER BY name")
            ->fetchAll(PDO::FETCH_ASSOC);
        $this->view('payer_accounts/form', [
            'title' => 'Ubah Rekening Payer',
            'account' => $account,
            'companies' => $companies,
        ]);
    }

    public function update(string $id): void
    {
        $this->permission('payer_account.manage');
        $before = $this->find((int) $id);
        $data = $this->validated();

        $db = App::db();
        $db->beginTransaction();
        try {
            if ((int) $data['is_default'] === 1) {
                $db->prepare('UPDATE payer_accounts SET is_default=0,updated_at=NOW() WHERE company_id=? AND id<>?')
                    ->execute([(int) $data['company_id'], (int) $id]);
            }

            $statement = $db->prepare(
                'UPDATE payer_accounts SET
                 company_id=?,account_no=?,account_name=?,bank_name=?,bank_branch=?,swift=?,currency=?,email=?,address=?,city=?,country=?,is_default=?,status=?,updated_at=NOW()
                 WHERE id=?'
            );
            $statement->execute([
                $data['company_id'], $data['account_no'], $data['account_name'], $data['bank_name'],
                $data['bank_branch'], $data['swift'], $data['currency'], $data['email'],
                $data['address'], $data['city'], $data['country'], $data['is_default'],
                $data['status'], (int) $id,
            ]);
            $db->commit();

            AuditService::log('update', 'payer_account', (int) $id, $before, $data);
            flash('success', 'Rekening payer berhasil diperbarui.');
            redirect('/payer-accounts');
        } catch (\Throwable $exception) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            remember_old($_POST);
            flash('danger', 'Rekening gagal diperbarui: ' . $exception->getMessage());
            redirect('/payer-accounts/' . (int) $id . '/edit');
        }
    }

    public function destroy(string $id): void
    {
        $this->permission('payer_account.manage');
        $before = $this->find((int) $id);

        $used = App::db()->prepare('SELECT COUNT(*) FROM transactions WHERE payer_account_id=? AND deleted_at IS NULL');
        $used->execute([(int) $id]);
        if ((int) $used->fetchColumn() > 0) {
            App::db()->prepare("UPDATE payer_accounts SET status='inactive',is_default=0,updated_at=NOW() WHERE id=?")
                ->execute([(int) $id]);
            AuditService::log('deactivate', 'payer_account', (int) $id, $before, ['status' => 'inactive']);
            flash('warning', 'Rekening sudah dipakai transaksi sehingga dinonaktifkan, bukan dihapus permanen.');
            redirect('/payer-accounts');
        }

        App::db()->prepare('DELETE FROM payer_accounts WHERE id=?')->execute([(int) $id]);
        AuditService::log('delete', 'payer_account', (int) $id, $before, null);
        flash('success', 'Rekening payer dihapus.');
        redirect('/payer-accounts');
    }

    /** @return array<string,mixed> */
    private function validated(): array
    {
        $companyId = (int) ($_POST['company_id'] ?? 0);
        $accountNo = trim((string) ($_POST['account_no'] ?? ''));
        $bankName = trim((string) ($_POST['bank_name'] ?? ''));
        if ($companyId <= 0 || $accountNo === '' || $bankName === '') {
            remember_old($_POST);
            flash('danger', 'Perusahaan, nomor rekening, dan nama bank wajib diisi.');
            redirect($_SERVER['HTTP_REFERER'] ?? '/payer-accounts');
        }

        $currency = strtoupper(trim((string) ($_POST['currency'] ?? 'IDR')));
        if (!preg_match('/^[A-Z]{3}$/', $currency)) {
            remember_old($_POST);
            flash('danger', 'Mata uang harus berupa kode tiga huruf, misalnya IDR atau USD.');
            redirect($_SERVER['HTTP_REFERER'] ?? '/payer-accounts');
        }

        $email = trim((string) ($_POST['email'] ?? ''));
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            remember_old($_POST);
            flash('danger', 'Format email tidak valid.');
            redirect($_SERVER['HTTP_REFERER'] ?? '/payer-accounts');
        }

        clear_old();
        return [
            'company_id' => $companyId,
            'account_no' => preg_replace('/\s+/', '', $accountNo) ?: $accountNo,
            'account_name' => trim((string) ($_POST['account_name'] ?? '')) ?: null,
            'bank_name' => $bankName,
            'bank_branch' => trim((string) ($_POST['bank_branch'] ?? '')) ?: null,
            'swift' => strtoupper(trim((string) ($_POST['swift'] ?? ''))) ?: null,
            'currency' => $currency,
            'email' => $email ?: null,
            'address' => trim((string) ($_POST['address'] ?? '')) ?: null,
            'city' => trim((string) ($_POST['city'] ?? '')) ?: null,
            'country' => trim((string) ($_POST['country'] ?? 'INDONESIA')) ?: 'INDONESIA',
            'is_default' => !empty($_POST['is_default']) ? 1 : 0,
            'status' => in_array((string) ($_POST['status'] ?? 'active'), ['active', 'inactive'], true)
                ? (string) $_POST['status']
                : 'active',
        ];
    }

    /** @return array<string,mixed> */
    private function find(int $id): array
    {
        $statement = App::db()->prepare(
            'SELECT pa.*,c.name company_name FROM payer_accounts pa JOIN companies c ON c.id=pa.company_id WHERE pa.id=? LIMIT 1'
        );
        $statement->execute([$id]);
        $record = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$record) {
            throw new \RuntimeException('Rekening payer tidak ditemukan.');
        }
        return $record;
    }
}
