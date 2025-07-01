import { HttpClient } from '@angular/common/http';
import { inject, Injectable, signal } from '@angular/core';
import { Observable, tap, retry, map, catchError, of, BehaviorSubject } from 'rxjs';
import { Token } from './token';
import { IToken } from '../models/interfaces';
import { User } from '../models/usuario';
import { Signal } from '@angular/core';
import { Router } from '@angular/router';


const _SERVER = 'http://localhost:9000';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private http = inject(HttpClient);// servicio para hacer peticiones HTTP
  private tokenService = inject(Token);
  public userActualS = signal(new User); // Usando signal para el usuario actual
  private router = inject(Router); // Inyectar el Router para gestionar alguna ruta por si quiero ir

  constructor() { }

  // ESTO SOLO ES PARA EL LOGEO PERO SOLO MANDA A LLAMAR A LOS TOKEN TODAVIA AQUI NO SE PEGAN LOS TOKEN EN EL LOCALSTORAGE
  login(datos: { identificacion: '', passw: '' }): Observable<any> {
    return this.http.patch<any>(`${_SERVER}/api/auth`, datos) // path y post lleva datos  entonces decvueelven algo 
      .pipe(// pipe es de rjxs  y tiene distintos funciones que se pueden usar para transformar los datos
        // retry es para reintentar la peticion en caso de error
        // tap es para hacer algo con los datos que se reciben sin modificarlo
        // map es para transformar los datos que se reciben
        // catchError es para manejar el error si ocurre
        retry(3), // reintentar 3 veces en caso de error
        tap((tokens) => { // el tap es para hacer algo con los datos que se reciben sin modificarlo
          // tokens es el objeto que se recibe de la respuesta del servidor
          // si el servidor devuelve un error, no se ejecuta este tap           
          // navegar al la pagina de inico si la autenticación es exitosa
          this.doLogin(tokens as IToken);
          console.log('Tokens recibidos:', tokens);
        }),
        map(() => true), // retornar true si la autenticación es exitosa ESTO SE PASA AL PADRE O AL QUE LO LLAMO 
        catchError((error) => {
          console.error('Error en autenticación:', error);
          return of(error.status)  // o puedes usar throwError si prefieres propagar el error
        })
      );
  }

  // si escribo get puedo usarlo sin parentesis el metodo 
  private doLogin(tokens: IToken) {
    this.tokenService.setTokens(tokens);
    this.userActualS.set(this.userActual); // Actualizar el usuario actual usando signal

  }

  // estoy logeado y eso lo hago verificando si tengo un token y si no esta expirado
  isLogedIn(): boolean {
    console.log(!!this.tokenService.getToken() && !this.tokenService.isTokenExpired());
    console.log('isLogedIn sacando el token viejo o nuevo  aveces queda el viejo', this.tokenService.getToken());

    return !!this.tokenService.getToken() && !this.tokenService.isTokenExpired();
  }
  loggOut() {
    if (this.isLogedIn()) {
      this.http.delete(`${_SERVER}/api/auth/${this.userActual.id}`, {}).subscribe({
        next: () => {
          console.log('Sesión cerrada correctamente');

        } // Limpiar el usuario actual al cerrar sesión
      });
      this.doLoggoOut();
    }
  }

  public get userActual(): User {
    if (!this.tokenService.decodeToken()) {
      return new User();
    }
    const tokend = this.tokenService.decodeToken();
    return new User({ id: tokend.sub, nombre: tokend.nom, rol: tokend.rol });
  }
  // si tengo un token lo elimino
  doLoggoOut(){
    if(this.tokenService.getToken()){
      this.tokenService.eliminarTokens();
     
    }
     this.userActualS.set(this.userActual); // Limpiar el usuario actual al cerrar sesión
      this.router.navigate(['/login']); // Redirigir al usuario a la página de inicio de sesión
  }

  



}