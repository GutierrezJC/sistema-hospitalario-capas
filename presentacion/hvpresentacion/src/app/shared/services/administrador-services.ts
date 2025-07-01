import { HttpClient, HttpParams } from '@angular/common/http';
import { inject, Injectable } from '@angular/core';
import { catchError, map, of } from 'rxjs';
import { TipoAdministrador } from '../models/interfaces';
const _SERVER = "http://localhost:9000"; // URL del servidor
@Injectable({
  providedIn: 'root'
})
export class AdministradorServices {
  private readonly http = inject(HttpClient);//hace que tu clase VisitantesServices tenga acceso a todo lo necesario para hacer peticiones HTTP (get, post, put, delete, etc.) al backend

 constructor() { }
  filtrar(parametros: any) {
    let params = new HttpParams;
    for (const prop in parametros) {
      params = params.append(prop, parametros[prop]);    }
    return this.http.get<any>(`${_SERVER}/api/administrador/filtrar/0/10`, { params: params });
  }
  
  guardar(administrador: any) {
    console.log('DATOS EN EL METODO DE ADMIN SERVICE  DE LINEA 20'+administrador);
    return this.http.post<any>(`${_SERVER}/api/administrador`, administrador);
  }

  eliminar (id: string ) {
    return this.http.delete<any>(`${_SERVER}/api/administrador/${id}`);
  }
  buscar(id: string) {
    console.log('ID EN EL METODO DE ADMIN SERVICE  DE LINEA 28'+id);
    return this.http.get<TipoAdministrador>(`${_SERVER}/api/administrador/${id}`);
  }
  editar(administrador: any ) {
    delete administrador.id_Administrador; // Eliminar el campo id si no es necesario en la actualización
    console.log('DATOS EN EL METODO DE ADMIN SERVICE  DE LINEA 32'+administrador);
    return this.http.put<any>(`${_SERVER}/api/administrador/${administrador.identificacion}`, administrador);
  }


  resetearPassw(id: string) {
      console.log(id + ' id desde el servicio linea 16 ');
      return this.http.patch(`${_SERVER}/api/usuario/reset/${id}`, {}).pipe(
        map(() => true),
        catchError((error) => {
          return of(error.status);
        })
      );
    }
}
