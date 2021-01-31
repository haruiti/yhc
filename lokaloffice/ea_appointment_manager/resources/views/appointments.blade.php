@extends('layouts.app')
@include('includes.head')
@section('content')


{{-- <div class="col-md-4 col-sm-4 col-xs-12">

    <span class="add-on input-group-addon">
    <input type="text" name="acFiltroPeriodo" id="acFiltroPeriodo"
        class="form-control date-range" autocomplete="off"
        placeholder="Período"/>

</div> --}}

@php date_default_timezone_set('America/Sao_Paulo') @endphp
<div class="panel panel-default">
    <div class="panel-body" id="panelTableCorretivas">
            <div class="container" id="panelTableCorretivas">



<table id="datatable-agendamentos" class="table table-striped table-bordered table">
    <thead class="thead-th">
        <tr>
            <th style="display:none;">Data</th>
            <th>Dia</th>
            <th>Semana</th>
            <th>Hora Inicial</th>
            <th>Hora Final</th>
            <th>Sala</th>
            <th>Observação</th>
            <th>Total de Horas</th>
            <th>Data limite p/ cancelamento</th>
            <th>Cancelar/Editar</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($users))

        @foreach($users as $user => $value)
        <tr>
            <td style="display:none;">
                   {{ $value->start_datetime }}
            </td>
            <td class="td" data-th="Dia:&nbsp&nbsp"&nbsp&nbsp>
                <span >
                {{ date('d/m/Y', strtotime(str_replace('-', '/', $value->start_datetime))) }}

                </span>
            </td>
            <td class="td" data-th="Semana:&nbsp&nbsp">
                <span>
                    @php $dayOfWeek= date("l", strtotime($value->start_datetime))  @endphp
                    @if($dayOfWeek=='Monday')
                        Segunda
                    @elseif ($dayOfWeek=='Tuesday')
                        Terça
                    @elseif ($dayOfWeek=='Wednesday')
                        Quarta
                    @elseif ($dayOfWeek=='Thursday')
                        Quinta
                    @elseif ($dayOfWeek=='Friday')
                        Sexta
                    @elseif ($dayOfWeek=='Saturday')
                        Sábado
                    @elseif ($dayOfWeek=='Sunday')
                        Domingo
                    @endif
                </span>
            </td>
            <td class="td" data-th="Hora Inicial:&nbsp&nbsp">
                <span>
                {{ date("H:i",strtotime($value->start_datetime)) }}
                </span>
            </td>
            <td class="td" data-th="Hora Final:&nbsp&nbsp">
                <span>
                {{ date("H:i",strtotime($value->end_datetime)) }}
                </span>
            </td>
            <td class="td" data-th="Sala:&nbsp&nbsp">
                <span >
                {{ $value->last_name }}
                </span>
            </td>
            <td class="td" data-th="Obs.:&nbsp&nbsp">
                <span >
                {{ $value->notes }}
                </span>
            </td>
            <td class="td" data-th="Total de Horas:&nbsp&nbsp">
                <span>
                {{ $value->name }}
                </span>
            </td>
            @php $dataLimite = strtotime('-8 hour',strtotime($value->start_datetime)) @endphp
            @php $dataAgora = strtotime('now') @endphp
            @php $dataDiff = $dataLimite -$dataAgora @endphp

            @if($dataDiff <= 0)
            <td class="td" data-th="Prazo p/ cancelamento:&nbsp&nbsp" style="color: red;">
                <span>
            @endif
            @if($dataDiff > 0 && $dataDiff <= 28800 )
            <td class="td" data-th="Prazo p/ cancelamento:&nbsp&nbsp" style="color: orange;">
                <span>
            @endif
            @if($dataDiff > 28800)
            <td class="td" data-th="Prazo p/ cancelamento:&nbsp&nbsp" style="color: green;">
                <span>
            @endif
                {{ date('d/m/Y H:i', strtotime(str_replace('-', '/',date('Y-m-d H:i',strtotime('-8 hour',strtotime($value->start_datetime)))))) }}


            </span>
            </td>
            <td class="td" data-th="Cancelar/Editar:&nbsp&nbsp">
                <a href="http://lokaloffice.yamatohipnoseclinica.com.br/index.php/appointments/index/{{ $value->hash }}"
                    class="btn btn-primary" id="btn_getDados" data-id="{{ $value->hash }}"><i
                        class="fas fa-edit"></i></a>

            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>
</div>
</div>
</div>
<div id="corretiva-detail-modal"></div>
@endsection

<script>

    var x = window.matchMedia("(max-width: 640px)")
    if (x.matches) { // If media query matches

        $('.nav_menu').hide();

        $( document ).ready(function() {

            $('#datatable-agendamentos').removeClass('nowrap');

            if(<?php echo count($users); ?> > 1){
                var tamanho = "400px";
            }else{
                var tamanho = "";
            }
            $('#datatable-agendamentos').DataTable({
                dom: 'Bfrtip',
                buttons: [],
                paging: true,
                lengthMenu: [ [7, 14, 28, -1], [7, 14, 28, "Tudo"] ],
                fixedColumns: {
                    leftColumns: 1
                },
                language: {
                            "lengthMenu": "Exibindo _MENU_ Registros por página",
                            "zeroRecords": "Nenhum registro encontrado",
                            "info": "Mostrando página _PAGE_ de _PAGES_",
                            "infoEmpty": "Nenhum registro encontrado",
                            "sSearch": "Pesquisar: ",
                            "oPaginate": {
                                "sNext": "Próximo",
                                "sPrevious": "Anterior",
                                "sFirst": "Primeiro",
                                "sLast": "Último"
                            },
                            "infoFiltered": "(filtrados de um total de _MAX_ registros)"
                            }
                });
        });

    } else {
        $( document ).ready(function() {

            $('#datatable-agendamentos').addClass('nowrap');

        if(<?php echo count($users); ?> > 1){
            var tamanho = "400px";
        }else{
            var tamanho = "";
        }
                $('#datatable-agendamentos').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'csv',
                            text: 'CSV',
                            title: "Hersil Corretivas"
                        },
                        {
                            extend: 'excel',
                            text: 'XLSX',
                            title: "Hersil Corretivas"
                        }
                    ],
                    paging:         true,
                    lengthMenu: [ [7, 14, 28, -1], [7, 14, 28, "Tudo"] ],
                    fixedColumns: {
                        leftColumns: 1
                    },
                    language: {
                                "lengthMenu": "Exibindo _MENU_ Registros por página",
                                "zeroRecords": "Nenhum registro encontrado",
                                "info": "Mostrando página _PAGE_ de _PAGES_",
                                "infoEmpty": "Nenhum registro encontrado",
                                "sSearch": "Pesquisar: ",
                                "oPaginate": {
                                    "sNext": "Próximo",
                                    "sPrevious": "Anterior",
                                    "sFirst": "Primeiro",
                                    "sLast": "Último"
                                },
                                "infoFiltered": "(filtrados de um total de _MAX_ registros)"
                                }
                });
            });
        }




</script>


