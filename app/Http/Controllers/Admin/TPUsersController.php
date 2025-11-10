<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EndUser\TPUser;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TPUsersController extends Controller
{

    public function index(Request $request, $status = 'all')
    {
        $statuses = [
            'all' => 'ALL APPLICATIONS',
            'pending' => 'INITIAL SUBMIT',
            'correction-required' => 'CORRECTION REQUESTED',
            'resubmitted' => 'RESUBMITTED',
            'verified' => 'VERIFIED',
            'final-payment' => 'FINAL SUBMIT',
            'approved' => 'APPROVED',
            'rejected' => 'REJECTED',
            'blacklisted' => 'BLACKLISTED',
        ];


       if (!array_key_exists($status, $statuses)) {
            $status = 'all';
        }

        return view('admin.tpusers.index', compact('status', 'statuses'));
    }


    public function data($status = 'all')
    {
        $query = TPUser::with(['user', 'centers'])
                        ->whereHas('user', function ($q) {
                            $q->where('role', 'TP User');
                        });


        if ($status !== 'all') {
            switch ($status) {
                case 'saved':
                    $query->where('t_p_users.status', 'Saved');
                    break;
                case 'pending':
                    $query->where('t_p_users.status', 'Pending');
                    break;
                case 'assigned':
                    $query->where('t_p_users.internal_status', 'Assigned');
                    break;
                case 'under_review':
                    $query->where('t_p_users.internal_status', 'Under Review');
                    break;
                case 'correction-required':
                    $query->where('t_p_users.status', 'Correction Required')->where('t_p_users.internal_status', 'Waiting for User');
                    break;
                case 'resubmitted':
                    $query->where('t_p_users.status', 'Resubmitted')->where('t_p_users.internal_status', 'Awaiting Review');
                    break;
                case 'verified':
                    $query->where('t_p_users.status', 'Verified')->where('t_p_users.internal_status', 'Docs Checked');
                    break;
                case 'final-payment':
                    $query->where('t_p_users.status', 'Payment Done')->where('t_p_users.internal_status', 'Awaiting Approval');
                    break;
                case 'approved':
                    $query->where('t_p_users.status', 'Approved')->where('t_p_users.internal_status', 'Completed');
                    break;
                case 'rejected':
                    $query->where('t_p_users.status', 'Rejected')->where('t_p_users.internal_status', 'Closed');
                    break;
                case 'blacklisted':
                    $query->where('t_p_users.status', 'Blacklisted')->where('t_p_users.internal_status', 'Closed');
                    break;
            }
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('username', function ($row) {
                return $row->user ? e($row->user->username) : '-';
            })
            ->addColumn('tp_info', function ($row) {

                $tpName = $row->tp_name ?? '-';
                $tpContact_number = $row->contact_number ?? '-';
                $tpEmail = $row->email ?? '-';
                $tpAddress = trim(($row->address ?? '') . ' ' . ($row->district ?? '') . ' ' . ($row->state ?? ''));

                $address = wordwrap(e($tpAddress), 35, '<br>');
                $html = '
                    <p class="mb-1"><strong>TP Name :</strong> ' . e($tpName) . '</p>
                    <p class="mb-1"><strong>TP Contact :</strong> ' . e($tpContact_number) . '</p>
                    <p class="mb-1"><strong>TP Email :</strong> ' . e($tpEmail) . '</p>
                    <p class="mb-0"><strong>Address :</strong> ' . $address . '</p>
                ';
                return $html;
            })
            ->addColumn('tc_info', function ($row) {
                    if ($row->centers->isEmpty()) {
                        return '<span class="text-muted">No Centers Found</span>';
                    }

                    $centersHtml = '';
                    foreach ($row->centers as $center) {
                        $tcType = $center->tc_type == 'other' && $center->tc_type_other
                            ? str_replace('_', ' ', $center->tc_type_other)
                            : str_replace('_', ' ', $center->tc_type);

                        $tcType = wordwrap(e($tcType), 35, '<br>');

                        $address = trim(($row->address ?? '') . ' ' . ($row->district ?? '') . ' ' . ($row->state ?? ''));
                        $address = wordwrap(e($address), 35, '<br>');

                        $centersHtml .= '
                            <div class="mb-2 pb-1">
                                <p class="mb-1"><strong>TC Name :</strong> ' . e($center->tc_name ?? '-') . '</p>
                                <p class="mb-0"><strong>TC Type :</strong> ' . $tcType . '</p>
                                <p class="mb-0"><strong>TC Address :</strong> ' . $address . '</p>
                            </div>
                        ';
                    }

                    return $centersHtml;
            })
            ->addColumn('created_at', function ($row) {
                return $row->created_at ? $row->created_at->format('M d, Y') : '-';
            })
            ->addColumn('status', function ($row) {
                $statusLabel = '';

                switch ($row->status) {
                    case 'Saved':
                        $statusLabel = '<span class="badge badge-secondary d-inline-flex align-items-center badge-sm">Registration Pending</span>';
                        break;
                    case 'Pending':
                        $statusLabel = '<span class="badge badge-primary  d-inline-flex align-items-center badge-sm">Pending</span>';
                        break;
                    case 'Resubmitted':
                        $statusLabel = '<span class="badge badge-info badge-sm">Resubmitted</span>';
                        break;
                    case 'Verified':
                        $statusLabel = '<span class="badge badge-primary badge-sm" >Verified</span>';
                        break;
                    case 'Payment Done':
                        $statusLabel = '<span class="badge badge-success badge-sm">Payment Done</span>';
                        break;
                    case 'Approved':
                        $statusLabel = '<span class="badge badge-success badge-sm">Approved</span>';
                        break;
                    case 'Rejected':
                        $statusLabel = '<span class="badge badge-danger badge-sm">Rejected</span>';
                        break;
                    case 'Blacklisted':
                        $statusLabel = '<span class="badge badge-dark badge-sm">Blacklisted</span>';
                        break;
                    case 'Correction Required':
                        $statusLabel = '<span class="badge badge-danger badge-sm">Correction Required</span>';
                        break;
                    default:
                        $statusLabel = '<span class="badge badge-light badge-sm">Unknown</span>';
                        break;
                }

                // Optional: show internal_status too
                if (!empty($row->internal_status)) {
                    $statusLabel .= '<br><small class="text-muted">' . e($row->internal_status) . '</small>';
                }

                return $statusLabel;
            })
            ->addColumn('actions', function ($row) {
                $actions =  '
                        <div class="btn-group dropend">
                            <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                                Action
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-lg-start ">';

                $actions .= '<li><a class="dropdown-item view-details" href="javascript:void(0);" data-id="' . $row->id . '">👁️ View Application</a></li>';
                $actions .= '<li><a class="dropdown-item download-docs" href="javascript:void(0);" data-id="' . $row->id . '">⬇️ Download Documents </a></li>';

                if ($row->status === 'Approved' && $row->internal_status === 'Completed') {
                    $actions .= '<li><a class="dropdown-item view-changes" href="javascript:void(0);" data-id="' . $row->id . '">📝 View Changes</a></li>';
                    $actions .= '<li><a class="dropdown-item blacklist-app" href="javascript:void(0);" data-id="' . $row->id . '">🚫 Blacklist Application</a></li>';
                }

                $assignStaffStatuses = ['Pending', 'Payment Done', 'Resubmitted', 'Correction Required', 'Under Review'];
                if (in_array($row->status, $assignStaffStatuses) || in_array($row->internal_status, ['Unassigned', 'Awaiting Review', 'Waiting for User', 'Under Review'])) {
                    $actions .= '<li><a class="dropdown-item assign-staff" href="javascript:void(0);" data-id="' . $row->id . '">👨‍💼 Assign To Staff</a></li>';
                }

                if ($row->status !== 'Approved') {
                    $actions .= '<li><a class="dropdown-item correction-send" href="javascript:void(0);" data-id="' . $row->id . '">✏️ Correction Send to TPUser</a></li>';
                    $actions .= '<li><a class="dropdown-item reject-app" href="javascript:void(0);" data-id="' . $row->id . '">❌ Reject Application</a></li>';
                }

                $actions .= '</ul></div>';
                return $actions;

            })
            ->rawColumns(['tp_info', 'tc_info', 'status', 'actions'])

            // filters
            ->filterColumn('tp_info', function($query, $keyword) {
                $query->where('t_p_users.tp_name', 'like', "%{$keyword}%");
                $query->orWhere('t_p_users.legel_type_of_tp', 'like', "%{$keyword}%");
                $query->orWhere('t_p_users.pin_code', 'like', "%{$keyword}%");
                $query->orWhere('t_p_users.state', 'like', "%{$keyword}%");
                $query->orWhere('t_p_users.district', 'like', "%{$keyword}%");
                $query->orWhere('t_p_users.city', 'like', "%{$keyword}%");
                $query->orWhere('t_p_users.contact_number', 'like', "%{$keyword}%");
                $query->orWhere('t_p_users.email', 'like', "%{$keyword}%");
            })
            ->filterColumn('tc_info', function($query, $keyword) {
                $query->whereHas('centers', function($q) use ($keyword) {
                    $q->where('tc_name', 'like', "%{$keyword}%");
                });
            })
            ->make(true);
    }



    public function show($id)
    {
        $tpUser = TPUser::findOrFail($id);
        return view('admin.tpusers.show', compact('tpUser'));
    }


    public function makeZipWithFiles(Request $request){

        $ip_user = $request->tp_id;

        $zip = new \ZipArchive();
        $zip->open($ZipFileName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $filesAndPaths = scandir($basePath);

        foreach($filesAndPaths as $file)
        {
            if ($file != "." && $file != "..") {
                $file = $basePath.$file;
                if (! $zip->addFile($file, basename($file))) {
                    echo 'Could not add file to ZIP: ' . $file;
                }
            }
        }
        $zip->close();
        return response()->download($ZipFileName);
    }


}
