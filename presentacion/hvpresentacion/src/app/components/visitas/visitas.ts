import { AfterViewInit, Component, inject, signal } from '@angular/core';
import { VisitasServices } from '../../shared/services/visitas-services';
import { TipoVisitaFiltrada } from '../../shared/models/interfaces';
import { MatCardModule } from '@angular/material/card';
import { MatTableDataSource, MatTableModule } from '@angular/material/table';
import { MatIconModule } from '@angular/material/icon';
import { MatDialog } from '@angular/material/dialog';
import { DialogoGeneral } from '../forms/dialogo-general/dialogo-general';
import { FrmRegistroVisita } from '../forms/frm-registro-visita/frm-registro-visita';
import { FrmCambioEstado } from '../forms/frm-cambio-estado/frm-cambio-estado';
import { AuthService } from '../../shared/services/auth-service';

@Component({
  selector: 'app-visitas',
  templateUrl: './visitas.html',
  styleUrl: './visitas.css',
  imports: [MatCardModule, MatTableModule, MatIconModule],
})
export class Visitas implements AfterViewInit {

  private readonly visitasService = inject(VisitasServices);
  private readonly dialog = inject(MatDialog);
  public  servicioAutenticacion= inject(AuthService)
  columnas: string[] = [
    'id_visita',
    'identificacion_visitante',
    'nombre_completo_visitante',
    'identificacion_administrador',
    'motivo_visita',
    'fecha_entrada',
    'fecha_salida',
    'estado',
    'botonera'
  ];
  filtro: any;
  dataSource = signal(new MatTableDataSource<TipoVisitaFiltrada>());

  soloVisitasVisitantes() : boolean {
    return this.servicioAutenticacion.userActualS().rol === '2';
  }

  ngAfterViewInit(): void {
    if(this.soloVisitasVisitantes ()) {
      var id = this.servicioAutenticacion.userActualS().id;
      console.log("ID del usuario actual: linea 43 visitas ", id);
      this.filtro = {
        identificacion_visitante: id,
        identificacion_administrador: '',
        motivo_visita: '',
        estado: '',
        fecha_entrada: ''
      };
    }else{
      this.filtro = {
      identificacion_visitante: '',
      identificacion_administrador: '',
      motivo_visita: '',
      estado: '',
      fecha_entrada: ''
    };
    }
    this.filtrar();
  }

  filtrar() {
    this.visitasService.filtrar(this.filtro).subscribe({
      next: (data: TipoVisitaFiltrada[]) => {
        this.dataSource().data = data;
      },
      error: (err) => console.log(err)
    });
  }

  // onEditar(id: number) {
  //  // alert("Editar cliente con ID: " + id);
  //   this.visitasService.buscar(id)// busco el cliente por id
  //     .subscribe((data) => {// EN DATA ESTAN LOS DATOS DEL CLIENTE QUE SE VA A EDITAR
  //       //console.log("Editar Visita", data);// muestro el dialogo con los datos del cliente
  //       //this.mostrarDialogo("Editar Cliente", data);// LE PASO DATA OSEA AL CLIENTE DATOS  Y EN LA LINEA 
  //       const visita = Array.isArray(data) ? data[0] : data;
  //       const dialogRef = this.dialog.open(FrmCambioEstado, {
  //         data: {// este data es el que se recibe del otro lado 
  //           id_Visita: id,
  //           estado: visita.estado,
  //           datos:data
            
  //         }
  //       });
  //     })
  //     this.filtrar();

  // }
  onEditar(id: number) {
  this.visitasService.buscar(id)
    .subscribe((data) => {
      const visita = Array.isArray(data) ? data[0] : data;
      const dialogRef = this.dialog.open(FrmCambioEstado, {
        data: {
          id_Visita: id,
          estado: visita.estado,
          datos: data
        }
      });

      dialogRef.afterClosed().subscribe((result) => {
        if (result) { // Solo si se guardó/cambió algo
          this.filtrar();
        }
      });
    });
}
  onReset(id: number) {

  }
  onInfo(id: number) {

  }
  onNuevo() {
    {
      //alert('Funcionalidad de nueva visita ');
      const dialogRef = this.dialog.open(FrmRegistroVisita, {
        width: '50vw',
        maxHeight: '35rem',
        data: {
          title: 'Nuevo Visitante'
        },
        disableClose: true
      });

      dialogRef.afterClosed().subscribe({
        next: (res) => {

          this.filtrar(); // Recarga la tabla si se creó un visitante

        },
        error: (err) => console.log(err)
      });

    }
  }

  onEliminar(id: number) {

    //alert('Funcionalidad de eliminar visita ' + id);

    const dialogRef = this.dialog.open(DialogoGeneral, {
      data: {// este data es el que se recibe del otro lado 
        texto: '¿Está seguro de que desea eliminar esta visita?',
        icono: 'question_mark',
        textoAceptar: 'si',
        textoCancelar: 'no'// asi lo llamo en el httmls 

      }
    });
    dialogRef.afterClosed().subscribe(resul => {
      if (resul === true) {// ¿De dónde sale ese resul?
        // Sale del valor que pasas en [mat-dialog-close] en los botones de tu diálogo.
        this.visitasService.eliminar(id)
          .subscribe((res: any) => {
            // crear reseta el filtro
            this.dialog.open(DialogoGeneral, {
              data: {
                texto: 'Visita eliminada correctamente',
                icono: 'check_circle',
                textoAceptar: 'Aceptar',
              }
            })
            this.filtrar();
          })

      }
    })

  }
}
