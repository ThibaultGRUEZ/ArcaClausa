using Constellation.Package;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace MyBrain
{
    [StateObject]
    public class Values
    {
        public double Latitude { get; set; }
        public double Longitude { get; set; }
        public string Login { get; set; }
        public string Pass { get; set; }
    }

    
}

