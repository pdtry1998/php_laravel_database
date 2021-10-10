<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Hello , {{Auth::user()->name}}

           
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                <div class="card">
                        <dic class="card-header">แบบฟอร์มแก้ไขข้อมูล</dic>
                        <div class="card-body">
                            <form action="{{url('/department/update/'.$department->id)}}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="department_name">ชื่อแผนก</label>
                                    <input type="text" class="form-control" name="department_name" value="{{$department->department_name}}">
                                </div>
                                @error('department_name')
                                   <div class="my-2">
                                        <span class="text-danger">{{$message}}</span>
                                   </div>
                                @enderror
                                <br>
                                <input type="submit" value="อัพเดท" class="btn btn-success"/>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
