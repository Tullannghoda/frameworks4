@extends('layouts.master')
@section('title','Wilayah Indonesia')

@section('content')

<h3>Data Wilayah Indonesia</h3>

<div>
    <button id="btn-jquery" onclick="setMode('jquery')"><b>Ajax jQuery</b></button>
    <button id="btn-axios" onclick="setMode('axios')">Axios</button>
</div>

<p id="mode-label">Mode aktif: <b>Ajax jQuery</b></p>

<br>

<label>Provinsi</label><br>
<select id="sel-province">
<option value="0">Pilih Provinsi</option>

@foreach($provinces as $p)
<option value="{{$p->id}}">{{$p->name}}</option>
@endforeach

</select>

<br><br>

<label>Kabupaten / Kota</label><br>
<select id="sel-regency">
<option value="0">Pilih Kabupaten/Kota</option>
</select>

<br><br>

<label>Kecamatan</label><br>
<select id="sel-district">
<option value="0">Pilih Kecamatan</option>
</select>

<br><br>

<label>Kelurahan</label><br>
<select id="sel-village">
<option value="0">Pilih Kelurahan</option>
</select>

@endsection
@section('scripts')

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>

let activeMode = "jquery";

function setMode(mode){

    activeMode = mode;

    $('#sel-province').val(0);
    $('#sel-regency').html('<option value="0">Pilih</option>');
    $('#sel-district').html('<option value="0">Pilih</option>');
    $('#sel-village').html('<option value="0">Pilih</option>');

    $('#mode-label').html("Mode aktif : <b>"+mode+"</b>");
}

const token = "{{ csrf_token() }}";


// ================= JQUERY =================

function loadRegency(id){

    $.ajax({
        url:"{{route('wilayah.regency')}}",
        method:"POST",
        data:{
            _token:token,
            province_id:id
        },
        success:function(res){

            res.result.forEach(function(item){

                $('#sel-regency').append(
                    `<option value="${item.id}">${item.name}</option>`
                )

            })

        }

    })

}

function loadDistrict(id){

    $.ajax({

        url:"{{route('wilayah.district')}}",
        method:"POST",
        data:{
            _token:token,
            regency_id:id
        },

        success:function(res){

            res.result.forEach(function(item){

                $('#sel-district').append(
                    `<option value="${item.id}">${item.name}</option>`
                )

            })

        }

    })

}

function loadVillage(id){

    $.ajax({

        url:"{{route('wilayah.village')}}",
        method:"POST",
        data:{
            _token:token,
            district_id:id
        },

        success:function(res){

            res.result.forEach(function(item){

                $('#sel-village').append(
                    `<option value="${item.id}">${item.name}</option>`
                )

            })

        }

    })

}


// ================= AXIOS =================

function loadRegencyAxios(id){

    axios.post("{{route('wilayah.regency')}}",{

        _token:token,
        province_id:id

    }).then(function(res){

        res.data.result.forEach(function(item){

            $('#sel-regency').append(
                `<option value="${item.id}">${item.name}</option>`
            )

        })

    })

}

function loadDistrictAxios(id){

    axios.post("{{route('wilayah.district')}}",{

        _token:token,
        regency_id:id

    }).then(function(res){

        res.data.result.forEach(function(item){

            $('#sel-district').append(
                `<option value="${item.id}">${item.name}</option>`
            )

        })

    })

}

function loadVillageAxios(id){

    axios.post("{{route('wilayah.village')}}",{

        _token:token,
        district_id:id

    }).then(function(res){

        res.data.result.forEach(function(item){

            $('#sel-village').append(
                `<option value="${item.id}">${item.name}</option>`
            )

        })

    })

}


// ================= EVENT =================

$(document).ready(function(){

    $('#sel-province').change(function(){

        let id=$(this).val();

        $('#sel-regency').html('<option value="0">Pilih</option>');
        $('#sel-district').html('<option value="0">Pilih</option>');
        $('#sel-village').html('<option value="0">Pilih</option>');

        if(id==0) return;

        if(activeMode=="jquery"){

            loadRegency(id)

        }else{

            loadRegencyAxios(id)

        }

    })


    $('#sel-regency').change(function(){

        let id=$(this).val();

        $('#sel-district').html('<option value="0">Pilih</option>');
        $('#sel-village').html('<option value="0">Pilih</option>');

        if(id==0) return;

        if(activeMode=="jquery"){

            loadDistrict(id)

        }else{

            loadDistrictAxios(id)

        }

    })


    $('#sel-district').change(function(){

        let id=$(this).val();

        $('#sel-village').html('<option value="0">Pilih</option>');

        if(id==0) return;

        if(activeMode=="jquery"){

            loadVillage(id)

        }else{

            loadVillageAxios(id)

        }

    })


})

</script>

@endsection