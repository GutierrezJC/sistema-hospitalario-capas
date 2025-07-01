import { Component, inject } from '@angular/core';
import { MatIconModule } from '@angular/material/icon';
import {MatListModule    } from '@angular/material/list';
import { RouterModule } from '@angular/router';
import { AuthService } from '../../shared/services/auth-service';

@Component({
  selector: 'app-side-var',
  imports: [MatIconModule,MatListModule,RouterModule],// icon module es de matirial y para los 
  templateUrl: './side-var.html',
  styleUrls: ['./side-var.css']
})
export class SideVar {
  public  srvAuth= inject(AuthService)
  

}

