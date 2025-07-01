import { AfterViewInit, Component, inject, signal, ViewChild } from '@angular/core';
import { TipoVisitante } from '../../shared/models/interfaces';
import { VisitantesServices } from '../../shared/services/visitantes-services';
import { MatCardModule } from '@angular/material/card';
import { MatTableDataSource, MatTableModule } from '@angular/material/table';
import { MatIconModule } from '@angular/material/icon';
import { MatDialog } from '@angular/material/dialog';
import { FrmVisitantes } from '../forms/frm-visitantes/frm-visitantes';
import { DialogoGeneral } from '../forms/dialogo-general/dialogo-general';
import { UsuarioServices } from '../../shared/services/usuario-services';
import { MatExpansionModule } from '@angular/material/expansion';// importar el modulo de expansion para usarlo en el formulario de cliente
import { MatPaginatorModule } from '@angular/material/paginator';// importar el modulo de paginacion para usarlo en la tabla de clientes
import { MatPaginator } from '@angular/material/paginator';
import { MatInputModule } from '@angular/material/input';
import { MatFormFieldModule } from '@angular/material/form-field';// importar el modulo de formulario para usarlo en el formulario de cliente








@Component({
  selector: 'app-visitantes',
  imports: [MatCardModule, MatTableModule, MatIconModule, MatExpansionModule, MatPaginatorModule, MatInputModule, MatFormFieldModule],
  templateUrl: './visitantes.html',
  styleUrl: './visitantes.css'
})
export class Visitantes implements AfterViewInit {
  private readonly visitanteSrv = inject(VisitantesServices);
  private readonly dialog = inject(MatDialog);
  private readonly usuariojSrv = inject(UsuarioServices); // Asegúrate de que este servicio esté correctamente inyectado
  @ViewChild(MatPaginator) paginator!: MatPaginator; // inyecta el paginador de la tabla, para paginar los datos de la tabla, se usa el decorador ViewChild para acceder al paginador desde el componente 
  // PARA LA TABLA 
  panelOpenState = signal(false);
  columnas: string[] = ['identificacion', 'nombre', 'apellido1', 'apellido2', 'telefono', 'correo', 'sector_laboral', 'botonera'];
  filtro: any;
  // signal es una funcion de angular para crear un objeto reactivo en este cado lo que hago es pasarle 
  dataSource = signal(new MatTableDataSource<TipoVisitante>());

  // apenas se carga el componente, se inicializa el filtro y se carga la lista de visitantes
  ngAfterViewInit(): void {
    this.filtro = { identificacion: '', nombre: '', apellido1: '', apellido2: '' };
    this.filtrar();
  }
  onFiltroChange(f: any) {
    this.filtro = f;
    this.filtrar();
  }
  limpiarFiltros() {
    this.restablecerFiltro();// resetea el filtro para que se muestren todos los datos
    (document.querySelector('#fidUsuario') as HTMLInputElement).value = '';
    (document.querySelector('#nombre') as HTMLInputElement).value = '';
    (document.querySelector('#apellido1') as HTMLInputElement).value = '';
    (document.querySelector('#apellido2') as HTMLInputElement).value = '';

  }
  restablecerFiltro() {
    this.filtro = { idCliente: '', nombre: '', apellido1: '', apelllido2: '' };// le paso el filtro vacio para que me traiga todos los datos
    this.filtrar();
  }



  //Cualquier método de HttpClient (get, post, put, delete) devuelve un observable.
  //El data que recibes es la respuesta del servidor (lo que retorna tu API).
  filtrar() {
    this.visitanteSrv.filtrar(this.filtro).subscribe({
      next: (data: any) => {
        this.dataSource.set(data);
      },
      error: (err) => console.log(err)
    });
  }

  // pra crear uno nuevo pero recordar que el en el form borro idvisitante por eso sirve 
  onNuevo() {
    //alert('Funcionalidad de nuevo visitante on nuevo');
    const dialogRef = this.dialog.open(FrmVisitantes, {
      width: '50vw',
      maxHeight: '35rem',
      data: {
        title: 'Nuevo Visitante'
      },
      disableClose: true
    });

    dialogRef.afterClosed().subscribe({
      next: (res) => {
        if (res !== false) {
          this.filtrar(); // Recarga la tabla si se creó un visitante
        }
      },
      error: (err) => console.log(err)
    });

  }


  // Puedes agregar métodos para mostrar diálogos, editar, eliminar, etc., igual que en cliente

  onEditar(id: number) {
    //alert('Funcionalidad de editar visitante on editar: ' + id);
    this.visitanteSrv.buscar(id).subscribe((data) => {
      const dialogRef = this.dialog.open(FrmVisitantes, {// el frm recive titulo y los datos del visitante
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


  onEliminar(id: string) {
    //HAGO EL DIALOGO REEF PARA SABER QUE ME DEVOLVIO EL COMPONTNETE  
    const dialogRef = this.dialog.open(DialogoGeneral, {
      data: {
        texto: '¿Está seguro de que desea eliminar este visitante ?',
        icono: 'question_mark',
        textoAceptar: 'si',
        textoCancelar: 'no'// asi lo llamo en el httmls 

      }
    });




    dialogRef.afterClosed().subscribe(resul => {
      if (resul === true) {// RESULT ES EL BOTON QUE SE PULSA EN EL DIALOGO
        this.visitanteSrv.eliminar(id)
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



  onReset(id: number) {
    this.visitanteSrv.buscar(id)
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
  onInfo(id_Visitante: number) {
    // Lógica para editar
    console.log('Editar visitante', id_Visitante);
  }

}
