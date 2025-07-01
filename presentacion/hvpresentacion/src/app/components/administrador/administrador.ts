import { AfterViewInit, Component, inject, signal } from '@angular/core';
import { TipoAdministrador, TipoVisitante } from '../../shared/models/interfaces';
import { MatCardModule } from '@angular/material/card';
import { MatTableDataSource, MatTableModule } from '@angular/material/table';
import { MatIconModule } from '@angular/material/icon';
import { MatDialog } from '@angular/material/dialog';
import { DialogoGeneral } from '../forms/dialogo-general/dialogo-general';
import { AdministradorServices } from '../../shared/services/administrador-services';
import { FrmAdministrador } from '../forms/frm-administrador/frm-administrador';
import { UsuarioServices } from '../../shared/services/usuario-services';
import { FrmEditAdmin } from '../forms/frm-edit-admin/frm-edit-admin';


@Component({
  selector: 'app-administrador',
  imports: [MatCardModule, MatTableModule, MatIconModule ],
  templateUrl: './administrador.html',
  styleUrl: './administrador.css'
})
export class Administrador {
  dataSource = signal(new MatTableDataSource<TipoAdministrador>());// data source es 
  private readonly administradorSRV = inject(AdministradorServices);
  private readonly dialog = inject(MatDialog);/// se usa cuando ocupo emergene un modal
   private readonly usuariojSrv = inject(UsuarioServices); 

  // En tu archivo administrador.ts
columnas: string[] = [
  'identificacion',
  'nombre',
  'apellido1',
  'apellido2',
  'telefono',
  'celular',
  'direccion',
  'correo',
  'botonera'
];

filtro :any
ngAfterViewInit(): void {
    this.filtro = { identificacion: '', nombre: '', apellido1: '', apellido2: '' };
    this.filtrar();
  }

filtrar() {
    this.administradorSRV.filtrar(this.filtro).subscribe({
        next: (data: any) => {
          this.dataSource.set(data);
          console.log('Datos filtrados:', data);
        },
        error: (err) => console.log(err)
      });
  }


  onNuevo() {
    const dialogRef = this.dialog.open(FrmAdministrador, {
          width: '50vw',
          maxHeight: '35rem',
          data: {
            // AQUI VAN LOS DAOTOS QUE QUIERO PASAR AL FORMULARIO ESCRIBIRLOS DENTRO DE DATA EL OBJETO
            title: 'Nuevo Admin'
          },
          disableClose: true
        });

        //LAMADA A PARA RECARGAR LA TABLA
      //LAMADA A PARA RECARGAR LA TABLA
  
}

onEliminar(identificacion: string) {
  const dialogRef = this.dialog.open(DialogoGeneral, {
      data: {
        texto: '¿Está seguro de que desea eliminar este visitante ?',
        icono: 'question_mark',
        textoAceptar: 'si',
        textoCancelar: 'no'// asi lo llamo en el httmls 

      }
    });




    dialogRef.afterClosed().subscribe(resul => {
      if (resul === true) {// result es el valor de los botones que usamos true o false 
        this.administradorSRV.eliminar(identificacion)
          .subscribe((res: any) => {
            // crear reseta el filtro
            this.dialog.open(DialogoGeneral, {
              data: {
                texto: 'visitante eliminado correctamente',
                icono: 'check_circle',
                textoAceptar: 'Aceptar',
              }
            })
          })
      }
    })
}

onInfo(id_Administrador: number) {
  // Lógica para editar
  console.log('Editar administrador', id_Administrador);
}

onEditar(id: string) {

  this.administradorSRV.buscar(id).subscribe((data) => {
        console.log("Datos del administrador a editar", data);// muestro el dialogo con los datos del cliente
        const dialogRef = this.dialog.open(FrmEditAdmin, {// el frm recive titulo y los datos del visitante
          width: '50vw',
          maxHeight: '35rem',
          data: {
            title: 'Editar Visitante',
            datos: data
          },
          disableClose: true
        });
  
        dialogRef.afterClosed().subscribe({
          next: (res) => {
            if (res !== false) {
              this.filtrar();
            }
          },
          error: (err) => console.log(err)
        });
      });
}

onReset(id:string){
  console.log('medotodo linea 114 de administrador ts ', id);
   this.administradorSRV.buscar(id)
      .subscribe({
        next: (data) => {
          console.log("Restablecer contraseña", data);// muestro el dialogo con los datos del cliente

          const dialogRef = this.dialog.open(DialogoGeneral, {
            data: {// este data es el que se recibe del otro lado
              texto: `¿Está seguro de que desea reestablecer la contraseña de ${data.nombre}?`,
              icono: 'question_mark',
              textoAceptar: 'si',
              textoCancelar: 'no'// asi lo llamo en el httmls 

            }
          });
          dialogRef.afterClosed().subscribe(resul => {
            if (resul === true) {// tipo de datoa y dato
              this.usuariojSrv.resetearPassw(data.identificacion)
                .subscribe((res) => { // en el suscribe se recisbe el resultado de la peticion pero como no devuelve nada, se puede omitir el parametro

                  this.dialog.open(DialogoGeneral, {
                    data: {
                      texto: 'Contraseña restablecida correctamente',
                      icono: 'check_circle',
                      textoAceptar: 'Aceptar',
                    }
                  })
                })
            }
          })
        }
      })
  }



}


