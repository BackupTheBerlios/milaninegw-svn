function Move() {
   
   this.Perform = function(FormName, CurrentPosition, NewPosition) {
      // Get the contents & ID of the current position
      var CurrentSlot = document.getElementById("Slot_"+CurrentPosition);
      var CurrentContents = CurrentSlot.innerHTML;
      var CurrentInput = document[FormName]["Sort_"+CurrentPosition];
      var CurrentID = CurrentInput.value;
      
      // Get the contents & ID of the new position
      var NewSlot = document.getElementById("Slot_"+NewPosition);
      var NewContents = NewSlot.innerHTML;
      var NewInput = document[FormName]["Sort_"+NewPosition];
      var NewID = NewInput.value;

      // Switch them
      if (CurrentSlot != null && CurrentInput != null && NewSlot != null && NewInput != null) {
         CurrentSlot.innerHTML = NewContents;
         CurrentInput.value = NewID;
         NewSlot.innerHTML = CurrentContents;
         NewInput.value = CurrentID;
      }
   }
   
   this.Up = function(FormName, CurrentPosition) {
      var NewPosition = CurrentPosition - 1;
      if (NewPosition < 1) {
         // Do nothing
      } else {
         this.Perform(FormName, CurrentPosition, NewPosition);
      }
   }
   
   this.Down = function(FormName, CurrentPosition) {
      var NewPosition = CurrentPosition + 1;
      var MaxPosition = document[FormName].SortItemCount.value;
      if (NewPosition > MaxPosition) {
         // Do nothing
      } else {
         this.Perform(FormName, CurrentPosition, NewPosition);
      }
   }
   
   this.Top = function(FormName, CurrentPosition) {
      var NewPosition = 1;
      if (NewPosition == CurrentPosition) {
         // Do nothing
      } else {
         this.Perform(FormName, CurrentPosition, NewPosition);
         // Now slide the misplaced item up the list until it reaches the second from top spot
         NewPosition = CurrentPosition;
         while (NewPosition > 2) {
            this.Up(FormName, NewPosition);
            NewPosition--;            
         }
      }
   }
   
   this.Bottom = function(FormName, CurrentPosition) {
      var MaxPosition = document[FormName].SortItemCount.value;
      var NewPosition = MaxPosition;
      if (CurrentPosition == NewPosition) {
         // Do nothing
      } else {
         this.Perform(FormName, CurrentPosition, NewPosition);
         // Now slide the misplaced item down the list until it reaches the second last spot
         NewPosition = CurrentPosition;
         while (NewPosition < (MaxPosition - 1)) {
            this.Down(FormName, NewPosition);
            NewPosition++;            
         }
      }
   }
}

function MoveUp(FormName, CurrentPosition) {
   new Move().Up(FormName, CurrentPosition);
}
function MoveTop(FormName, CurrentPosition) {
   new Move().Top(FormName, CurrentPosition);
}
function MoveBottom(FormName, CurrentPosition) {
   new Move().Bottom(FormName, CurrentPosition);
}
function MoveDown(FormName, CurrentPosition) {
   new Move().Down(FormName, CurrentPosition);
}
function ActOnItem(FormName, CurrentPosition, Url) {
   var ClickedID = document[FormName]["Sort_"+CurrentPosition].value;
   document.location = Url+ClickedID;
}