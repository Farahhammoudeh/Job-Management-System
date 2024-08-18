<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Mail\JobPosted;
use App\Models\Employer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;

class JobController extends Controller
{
    public function index()
    {
        $jobs = Job::with('employer')->latest()->simplePaginate(3);

        return view('jobs.index', [
            'jobs' => $jobs
        ]);
    }

    public function create()
    {
        return view('jobs.create');
    }

    public function show(Job $job)
    {
        return view('jobs.show', ['job' => $job]);
    }

    public function store(StoreJobRequest $request)
    {

        $job = Job::create([
            'employer_id' => 1,
            'title' => $request->title,
            'salary' => $request->salary,
        ]);

        Mail::to($job->employer->user)->queue(
            new JobPosted($job)
        );

        return redirect('/jobs');
    }

    public function edit(Job $job)
    {
        $this->authorize('edit', $job);

        return view('jobs.edit', ['job' => $job]);
    }

    public function update(UpdateJobRequest $request, Job $job)
    {
        $this->authorize('update', $job);

        $job->update([
            'title' => $request->title,
            'salary' => $request->salary,
        ]);

        return redirect('/jobs/' . $job->id);
    }

    public function destroy(Job $job)
    {
        $this->authorize('delete', $job);

        $job->delete();

        return redirect('/jobs');
    }
}
