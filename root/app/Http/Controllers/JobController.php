<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Models\Job;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //一覧画面
        $jobs = Job::orderByDesc('id')->paginate(20);
        return view('admin.jobs.index', [
            'jobs' => $jobs,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //新規画面
        return view('admin.jobs.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreJobRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreJobRequest $request)
    {
        //新規登録
        $job = Job::create([
            'name' => $request->name
        ]);
        return redirect(
            route('admin.jobs.show', ['job' => $job])
        )->with('messages.success', '新規登録が完了しました。');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function show(Job $job)
    {
        //詳細画面
        return view('admin.jobs.show', [
            'job' => $job,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function edit(Job $job)
    {
        //編集画面
        return view('admin.jobs.edit', [
            'job' => $job,
        ]);
    }

    public function confirm(UpdateJobRequest $request, Job $job)
    {
        // 更新確認画面
        $job->name = $request->name;
        return view('admin.jobs.confirm', [
            'job' => $job,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateJobRequest  $request
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateJobRequest $request, Job $job)
    {
        //更新
        $job->name = $request->name;
        $job->update();
        return redirect(
            route('admin.jobs.show', ['job' => $job])
        )->with('messages.success', '更新が完了しました。');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function destroy(Job $job)
    {
        //削除
        $job->delete();
        return redirect(route('admin.jobs.index'));
    }

    public function downloadCsv()
    {
        $csvRecords = self::getJobCsvRecords();
        return self::streamDownloadCsv('jobs.csv', $csvRecords);
    }

    private static function getJobCsvRecords(): array
    {
        $jobs = Job::orderByDesc('id')->get();
        $csvRecords = [
            ['ID','名称'],
        ];
        foreach ($jobs as $job) {
            $csvRecords[] = [$job->id, $job->name];
        }
        return $csvRecords;
    }

    private static function streamDownloadCsv
    (
        string $name,
        iterable $fieldsList,
        string $separator = ',',
        string $enclosure = '"',
        string $escape = "\\",
        string $eol = "\r\n"
    ){
        $contentType = 'text/plain';
        if ($separator === ',') {
            $contentType = 'text/csv';
        } elseif ($separator === "\t") {
            $contentType = 'text/tab-separated-values';
        }
        $headers = ['Content-Type' => $contentType];

        return response()->streamDownload(function () use ($fieldsList, $separator, $enclosure, $escape, $eol){
            $stream = fopen('php://output','w');
            foreach ($fieldsList as $fields) {
                fputcsv($stream, $fields, $separator, $enclosure, $escape);
                fwrite($stream,$eol);
            }
            fclose($stream);
        },$name,$headers);
    }

    public function downloadTsv()
    {
        $fieldsList = $this->getJobCsvRecords();
        return $this->streamDownloadCsv('jobs.tsv',$fieldsList,"\t");
    }
}
